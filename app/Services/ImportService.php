<?php

namespace App\Services;

use App\Contracts\Services\ApiClientInterface;
use App\Contracts\Services\PostTransformerInterface;
use App\DTOs\ImportResultDTO;
use App\DTOs\PostImportDTO;
use App\Enums\ImportSource;
use App\Enums\PostStatus;
use App\Exceptions\ApiFetchException;
use App\Exceptions\DuplicatePostException;
use App\Exceptions\ImportException;
use App\Repositories\PostRepositoryInterface;
use App\Services\ApiClients\FakeStoreClient;
use App\Services\ApiClients\JsonPlaceholderClient;
use App\Services\Transformers\JsonPlaceholderTransformer;
use App\Services\Transformers\FakeStoreTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImportService
{
    /**
     * Map of import sources to their clients and transformers
     *
     * @var array<string, array{client: ApiClientInterface, transformer: PostTransformerInterface}>
     */
    private array $sourceMap = [];

    public function __construct(
        private PostRepositoryInterface $postRepository,
        public JsonPlaceholderClient $jsonPlaceholderClient,
        public FakeStoreClient $fakeStoreClient,
        public JsonPlaceholderTransformer $jsonPlaceholderTransformer,
        public FakeStoreTransformer $fakeStoreTransformer,
    ) {
        $this->sourceMap = [
            ImportSource::JSON_PLACEHOLDER->value => [
                'client' => $jsonPlaceholderClient,
                'transformer' => $jsonPlaceholderTransformer,
            ],
            ImportSource::FAKE_STORE->value => [
                'client' => $fakeStoreClient,
                'transformer' => $fakeStoreTransformer,
            ],
        ];
    }

    /**
     * Import a post from the specified source
     *
     * @throws ImportException
     */
    public function importFrom(ImportSource $source): ImportResultDTO
    {
        $sourceConfig = $this->sourceMap[$source->value] ?? null;

        if (!$sourceConfig) {
            throw new ImportException("Unsupported import source: {$source->value}");
        }

        try {
            // Fetch data from API
            $data = $sourceConfig['client']->fetchRandom();

            if (!$data) {
                return ImportResultDTO::failure(
                    "Failed to fetch data from {$source->displayName()} API"
                );
            }

            // Transform data to DTO
            $dto = $sourceConfig['transformer']->transform($data);

            // Save post
            return $this->savePost($dto);
        } catch (ApiFetchException $e) {
            Log::error('API fetch failed during import', [
                'source' => $source->value,
                'error' => $e->getMessage(),
            ]);

            return ImportResultDTO::failure($e->getMessage());
        } catch (DuplicatePostException $e) {
            return ImportResultDTO::duplicate($e->existingPost, $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Unexpected error during import', [
                'source' => $source->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ImportResultDTO::failure(
                "An unexpected error occurred: {$e->getMessage()}"
            );
        }
    }

    /**
     * Import from JSONPlaceholder (backward compatibility)
     *
     * @deprecated Use importFrom(ImportSource::JSON_PLACEHOLDER) instead
     */
    public function importFromJsonPlaceholder(): ImportResultDTO
    {
        return $this->importFrom(ImportSource::JSON_PLACEHOLDER);
    }

    /**
     * Import from FakeStore (backward compatibility)
     *
     * @deprecated Use importFrom(ImportSource::FAKE_STORE) instead
     */
    public function importFromFakeStore(): ImportResultDTO
    {
        return $this->importFrom(ImportSource::FAKE_STORE);
    }

    /**
     * Save post to database with duplicate prevention
     *
     * @throws DuplicatePostException
     */
    private function savePost(PostImportDTO $dto): ImportResultDTO
    {
        // Check for duplicates
        $duplicate = $this->postRepository->findBySourceAndExternalId(
            $dto->source,
            $dto->externalId
        );

        if ($duplicate) {
            throw DuplicatePostException::create($duplicate);
        }

        // Create post as draft
        $post = $this->postRepository->createFromImport(
            $dto,
            Auth::id(),
            PostStatus::DRAFT
        );

        Log::info('Post imported successfully', [
            'post_id' => $post->id,
            'source' => $dto->source->value,
            'external_id' => $dto->externalId,
        ]);

        return ImportResultDTO::success($post);
    }
}


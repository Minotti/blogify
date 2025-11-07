# Nex Digital / Goodcommerce

## **Setup**

### Requirements

- PHP 8.4
- Composer 2.8.12
- SQLite (included) or MySQL/PostgreSQL

### Installation Steps

```bash
git clone [repository]
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

The application will be available at `http://localhost:8000`

---

## **Submission**

### Total Time Spent

**Approximately 2 hours**

Throughout development, I leveraged AI-assisted tools (Cursor) to accelerate code analysis, testing, and documentation — ensuring high-quality, well-structured solutions in less time.

---

### Explain Your Approach

#### Architecture Overview

I adopted a **layered architecture** with clear separation of concerns, following SOLID principles and Laravel best practices. The solution uses **Strategy Pattern** combined with **Adapter Pattern** to handle different API sources elegantly.

#### Key Design Decisions

**1. Transformers with DTOs Pattern**

The core of the solution is the **Transformer + DTO pattern**:

- **Transformers** (`PostTransformerInterface`): Each API source has its own transformer that implements a common interface. Transformers are responsible for converting raw API data into a standardized format.
- **DTOs (Data Transfer Objects)**: Instead of passing arrays around, I use immutable DTOs (`PostImportDTO`, `ImportResultDTO`) that provide:
  - **Type Safety**: Prevents runtime errors from typos or missing keys
  - **Validation**: DTOs validate data at creation time
  - **Immutability**: Using `readonly` properties ensures data integrity
  - **Self-Documentation**: Clear structure shows exactly what data is expected

**Example Flow:**
```
API Response (raw) → Transformer → PostImportDTO → ImportService → Database
```

This pattern ensures:
- **Extensibility**: Adding a new API requires only creating a new Transformer
- **Testability**: Each component can be tested in isolation
- **Maintainability**: Changes to one API don't affect others
- **Type Safety**: IDE autocomplete and static analysis work perfectly

**2. Service Layer Architecture**

- **ApiClients**: Handle HTTP communication with retry logic and proper error handling
- **Transformers**: Convert API-specific data to standardized DTOs
- **ImportService**: Orchestrates the import process, handles duplicate prevention, and manages business logic
- **Repository Pattern**: Abstracts database operations, making the code more testable

**3. Type Safety with Enums**

Using PHP 8.1+ Enums for `PostStatus` and `ImportSource` provides:
- Compile-time validation
- IDE autocomplete
- Helper methods (`isPublished()`, `displayName()`)
- No magic strings

**4. Custom Exceptions**

Domain-specific exceptions (`ApiFetchException`, `DuplicatePostException`) provide:
- Clear error messages
- Better error handling
- Easier debugging

---

### How to Add a New API Source

Adding a new API source is straightforward thanks to the architecture:

**Step 1: Create the API Client**

```php
// app/Services/ApiClients/NewApiClient.php
class NewApiClient implements ApiClientInterface
{
    public function fetchRandom(): ?array
    {
        // Implementation to fetch data from new API
    }
}
```

**Step 2: Create the Transformer**

```php
// app/Services/Transformers/NewApiTransformer.php
class NewApiTransformer implements PostTransformerInterface
{
    public function transform(array $data): PostImportDTO
    {
        return PostImportDTO::fromArray([
            'title' => $data['title'],
            'content' => $data['description'],
            'source' => ImportSource::NEW_API->value,
            'external_id' => (string) $data['id'],
        ]);
    }
}
```

**Step 3: Add to ImportSource Enum**

```php
// app/Enums/ImportSource.php
enum ImportSource: string
{
    // ... existing cases
    case NEW_API = 'newapi';
}
```

**Step 4: Register in ImportService**

```php
// app/Services/ImportService.php
public function __construct(
    // ... existing dependencies
    NewApiClient $newApiClient,
    NewApiTransformer $newApiTransformer,
) {
    $this->sourceMap = [
        // ... existing mappings
        ImportSource::NEW_API->value => [
            'client' => $newApiClient,
            'transformer' => $newApiTransformer,
        ],
    ];
}
```

**That's it!** The new API source is now fully integrated. The UI will automatically support it, and all existing functionality (duplicate prevention, error handling, etc.) works immediately.

**Benefits:**
- No changes needed in controllers or views
- No changes needed in database schema
- All business logic is reused
- Type-safe throughout

---

### Proposed Improvements

#### 1. Laravel Data Package for DTOs

**Current Implementation:**
Using manual DTOs with `readonly` properties and custom validation.

**Proposed Improvement:**
Replace custom DTOs with **Laravel Data** package, which provides:

- **Automatic validation** from rules
- **Automatic casting** from arrays/JSON
- **Better serialization** for API responses
- **Nested DTOs** support
- **Type transformations** out of the box

**Example:**
```php
use Spatie\LaravelData\Data;

class PostImportDTO extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public ImportSource $source,
        public string $externalId,
    ) {}
    
    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            // ...
        ];
    }
}
```

**Benefits:**
- Less boilerplate code
- Built-in validation
- Better integration with Laravel's validation system
- Automatic API resource transformation

#### 2. AI-Powered Content Filtering and Enrichment

**Scenario:** When importing posts from a trusted source, implement automated content curation.

**Proposed Solution:**

**Architecture:**
1. **Scheduled Job** (Cron): Periodically fetch new posts from APIs
2. **AI Analysis Service**: Use AI (OpenAI, Anthropic, or local model) to:
   - **Relevance Scoring**: Rate content relevance (0-100)
   - **Context Analysis**: Extract topics, sentiment, key themes
   - **Content Enrichment**: Generate summaries, tags, SEO metadata
   - **Quality Filtering**: Filter out low-quality or irrelevant content
3. **Queue System**: Process analysis asynchronously
4. **Admin Dashboard**: Review AI suggestions before publishing

**Implementation Example:**
```php
// app/Jobs/AnalyzePostContentJob.php
class AnalyzePostContentJob implements ShouldQueue
{
    public function handle(AIContentService $aiService, Post $post)
    {
        $analysis = $aiService->analyze($post->content);
        
        if ($analysis->relevanceScore < 70) {
            $post->update(['status' => PostStatus::REVIEW_NEEDED]);
            return;
        }
        
        $post->update([
            'ai_summary' => $analysis->summary,
            'ai_tags' => $analysis->tags,
            'relevance_score' => $analysis->relevanceScore,
        ]);
    }
}
```

**Benefits:**
- **Quality Control**: Only high-quality content gets published
- **SEO Optimization**: AI-generated metadata improves searchability
- **Content Curation**: Automatically categorize and tag content
- **Scalability**: Can process thousands of posts efficiently
- **Cost-Effective**: Only analyze posts that pass initial filters

**Additional Improvements:**
- **Caching**: Cache API responses to reduce external calls
- **Rate Limiting**: Implement rate limiting for API clients
- **Webhooks**: Support webhook-based imports for real-time updates
- **Batch Import**: Allow importing multiple items at once
- **Import History**: Track import history with success/failure rates
- **Testing**: Add comprehensive test coverage for all services

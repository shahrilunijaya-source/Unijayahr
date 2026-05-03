# Leave Management + Employee Profile Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add employee HR profile (personal, bank, emergency, education, documents) and full leave management (apply → manager approves → HR notified) to the Unijaya HR webapp.

**Architecture:** Separate `employee_*` tables keep HR data isolated from the auth `users` table. Leave uses three tables: `leave_types` (config), `leave_balances` (HR-set annual allocation per staff), and `leave_requests` (applications with approval trail). Filament auto-discovers all new resources and pages — no provider changes needed.

**Tech Stack:** Laravel 11, Filament 3, Spatie Permission, PHPUnit, SQLite (tests), MySQL (dev/prod)

---

## File Map

### New Migrations
- `database/migrations/2026_05_01_100000_create_employee_profiles_table.php`
- `database/migrations/2026_05_01_100001_create_employee_bank_details_table.php`
- `database/migrations/2026_05_01_100002_create_employee_emergency_contacts_table.php`
- `database/migrations/2026_05_01_100003_create_employee_education_table.php`
- `database/migrations/2026_05_01_100004_create_employee_documents_table.php`
- `database/migrations/2026_05_01_110000_create_leave_types_table.php`
- `database/migrations/2026_05_01_110001_create_leave_balances_table.php`
- `database/migrations/2026_05_01_110002_create_leave_requests_table.php`

### New Models
- `app/Models/EmployeeProfile.php`
- `app/Models/EmployeeBankDetail.php`
- `app/Models/EmployeeEmergencyContact.php`
- `app/Models/EmployeeEducation.php`
- `app/Models/EmployeeDocument.php`
- `app/Models/LeaveType.php`
- `app/Models/LeaveBalance.php`
- `app/Models/LeaveRequest.php`

### Modified
- `app/Models/User.php` — add hasOne/hasMany relations
- `database/seeders/DatabaseSeeder.php` — call new seeders
- `phpunit.xml` — enable SQLite in-memory for tests

### New Seeders
- `database/seeders/LeaveTypeSeeder.php`
- `database/seeders/LeaveBalanceSampleSeeder.php`

### New Filament Resources
- `app/Filament/Resources/EmployeeProfileResource.php` + `Pages/` (List, View, Edit)
- `app/Filament/Resources/EmployeeProfileResource/RelationManagers/EmergencyContactsRelationManager.php`
- `app/Filament/Resources/EmployeeProfileResource/RelationManagers/EducationRelationManager.php`
- `app/Filament/Resources/EmployeeProfileResource/RelationManagers/DocumentsRelationManager.php`
- `app/Filament/Resources/LeaveTypeResource.php` + `Pages/` (List, Create, Edit)
- `app/Filament/Resources/LeaveBalanceResource.php` + `Pages/` (List, Create, Edit)
- `app/Filament/Resources/LeaveRequestResource.php` + `Pages/` (List, View)

### New Filament Pages
- `app/Filament/Pages/MyLeavePage.php`
- `app/Filament/Pages/LeaveApprovalsPage.php`

### Modified Filament Pages
- `app/Filament/Pages/MyProfile.php` — add profile/bank/emergency/education/docs tabs

### New Blade Views
- `resources/views/filament/pages/my-leave.blade.php`
- `resources/views/filament/pages/leave-approvals.blade.php`

### New Tests
- `tests/Unit/LeaveBalanceTest.php`
- `tests/Unit/LeaveRequestTest.php`

---

## Task 1: Employee Profile Migrations

**Files:**
- Create: `database/migrations/2026_05_01_100000_create_employee_profiles_table.php`
- Create: `database/migrations/2026_05_01_100001_create_employee_bank_details_table.php`
- Create: `database/migrations/2026_05_01_100002_create_employee_emergency_contacts_table.php`
- Create: `database/migrations/2026_05_01_100003_create_employee_education_table.php`
- Create: `database/migrations/2026_05_01_100004_create_employee_documents_table.php`

- [ ] **Step 1: Create employee_profiles migration**

```php
// database/migrations/2026_05_01_100000_create_employee_profiles_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('nationality', 100)->default('Malaysian');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employee_profiles'); }
};
```

- [ ] **Step 2: Create employee_bank_details migration**

```php
// database/migrations/2026_05_01_100001_create_employee_bank_details_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('bank_name', 100)->nullable();
            $table->text('account_number')->nullable(); // encrypted in model
            $table->string('account_holder_name', 200)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employee_bank_details'); }
};
```

- [ ] **Step 3: Create employee_emergency_contacts migration**

```php
// database/migrations/2026_05_01_100002_create_employee_emergency_contacts_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('relationship', 100);
            $table->string('phone', 20);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employee_emergency_contacts'); }
};
```

- [ ] **Step 4: Create employee_education migration**

```php
// database/migrations/2026_05_01_100003_create_employee_education_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('institution', 300);
            $table->string('qualification', 100); // SPM, Diploma, Degree, Masters, PhD, Other
            $table->string('field_of_study', 200)->nullable();
            $table->smallInteger('year_completed')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employee_education'); }
};
```

- [ ] **Step 5: Create employee_documents migration**

```php
// database/migrations/2026_05_01_100004_create_employee_documents_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('category', [
                'ic_copy', 'offer_letter', 'contract', 'cert',
                'medical_cert', 'resignation_letter', 'other'
            ]);
            $table->string('label', 200)->nullable(); // required when category = other
            $table->string('file_path', 500);
            $table->unsignedInteger('file_size')->nullable(); // bytes
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employee_documents'); }
};
```

- [ ] **Step 6: Run migrations and confirm all 5 tables exist**

```bash
cd webapp-new
php artisan migrate
php artisan tinker --execute="collect(['employee_profiles','employee_bank_details','employee_emergency_contacts','employee_education','employee_documents'])->each(fn(\$t) => \Schema::hasTable(\$t) ? print(\"\$t OK\n\") : print(\"\$t MISSING\n\"));"
```

Expected: each table prints `OK`.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_05_01_10*
git commit -m "feat: employee profile migrations (profiles, bank, emergency, education, documents)"
```

---

## Task 2: Leave Migrations

**Files:**
- Create: `database/migrations/2026_05_01_110000_create_leave_types_table.php`
- Create: `database/migrations/2026_05_01_110001_create_leave_balances_table.php`
- Create: `database/migrations/2026_05_01_110002_create_leave_requests_table.php`

- [ ] **Step 1: Create leave_types migration**

```php
// database/migrations/2026_05_01_110000_create_leave_types_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_document')->default(false);
            $table->decimal('max_days_per_year', 5, 1)->nullable(); // null = unlimited
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('leave_types'); }
};
```

- [ ] **Step 2: Create leave_balances migration**

```php
// database/migrations/2026_05_01_110001_create_leave_balances_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('year');
            $table->decimal('allocated_days', 5, 1)->default(0);
            $table->decimal('carried_over', 5, 1)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'leave_type_id', 'year']);
        });
    }
    public function down(): void { Schema::dropIfExists('leave_balances'); }
};
```

- [ ] **Step 3: Create leave_requests migration**

```php
// database/migrations/2026_05_01_110002_create_leave_requests_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 4, 1);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('manager_note')->nullable();
            $table->timestamp('hr_notified_at')->nullable();
            $table->string('document_path', 500)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('leave_requests'); }
};
```

- [ ] **Step 4: Run migrations**

```bash
php artisan migrate
php artisan tinker --execute="collect(['leave_types','leave_balances','leave_requests'])->each(fn(\$t) => \Schema::hasTable(\$t) ? print(\"\$t OK\n\") : print(\"\$t MISSING\n\"));"
```

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_05_01_11*
git commit -m "feat: leave management migrations (types, balances, requests)"
```

---

## Task 3: Employee Profile Models

**Files:**
- Create: `app/Models/EmployeeProfile.php`
- Create: `app/Models/EmployeeBankDetail.php`
- Create: `app/Models/EmployeeEmergencyContact.php`
- Create: `app/Models/EmployeeEducation.php`
- Create: `app/Models/EmployeeDocument.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Create EmployeeProfile model**

```php
// app/Models/EmployeeProfile.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'user_id', 'date_of_birth', 'gender', 'marital_status',
        'address', 'city', 'state', 'postcode', 'nationality',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 2: Create EmployeeBankDetail model**

```php
// app/Models/EmployeeBankDetail.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBankDetail extends Model
{
    protected $fillable = ['user_id', 'bank_name', 'account_number', 'account_holder_name'];

    protected function casts(): array
    {
        return ['account_number' => 'encrypted'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 3: Create EmployeeEmergencyContact model**

```php
// app/Models/EmployeeEmergencyContact.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEmergencyContact extends Model
{
    protected $fillable = ['user_id', 'name', 'relationship', 'phone'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 4: Create EmployeeEducation model**

```php
// app/Models/EmployeeEducation.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    protected $fillable = ['user_id', 'institution', 'qualification', 'field_of_study', 'year_completed'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 5: Create EmployeeDocument model**

```php
// app/Models/EmployeeDocument.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    const CATEGORIES = [
        'ic_copy'           => 'IC Copy',
        'offer_letter'      => 'Offer Letter',
        'contract'          => 'Contract',
        'cert'              => 'Certificate',
        'medical_cert'      => 'Medical Certificate',
        'resignation_letter'=> 'Resignation Letter',
        'other'             => 'Other',
    ];

    protected $fillable = [
        'user_id', 'category', 'label', 'file_path',
        'file_size', 'mime_type', 'uploaded_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->category === 'other'
            ? ($this->label ?: 'Other')
            : (self::CATEGORIES[$this->category] ?? $this->category);
    }
}
```

- [ ] **Step 6: Add relations to User model**

Add to `app/Models/User.php` after the existing `subordinates()` method:

```php
public function employeeProfile(): HasOne
{
    return $this->hasOne(EmployeeProfile::class);
}

public function bankDetail(): HasOne
{
    return $this->hasOne(EmployeeBankDetail::class);
}

public function emergencyContacts(): HasMany
{
    return $this->hasMany(EmployeeEmergencyContact::class);
}

public function education(): HasMany
{
    return $this->hasMany(EmployeeEducation::class);
}

public function documents(): HasMany
{
    return $this->hasMany(EmployeeDocument::class);
}
```

Also add these use statements at the top of `User.php`:
```php
use Illuminate\Database\Eloquent\Relations\HasOne;
// HasMany already imported
```

- [ ] **Step 7: Commit**

```bash
git add app/Models/Employee*.php app/Models/User.php
git commit -m "feat: employee profile models (profile, bank, emergency, education, documents)"
```

---

## Task 4: Leave Models

**Files:**
- Create: `app/Models/LeaveType.php`
- Create: `app/Models/LeaveBalance.php`
- Create: `app/Models/LeaveRequest.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Create LeaveType model**

```php
// app/Models/LeaveType.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'name', 'code', 'is_paid', 'requires_document',
        'max_days_per_year', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_paid'             => 'boolean',
            'requires_document'   => 'boolean',
            'is_active'           => 'boolean',
            'max_days_per_year'   => 'decimal:1',
        ];
    }

    public function balances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public static function active(): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('is_active', true);
    }
}
```

- [ ] **Step 2: Create LeaveBalance model**

```php
// app/Models/LeaveBalance.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id', 'leave_type_id', 'year',
        'allocated_days', 'carried_over',
    ];

    protected function casts(): array
    {
        return [
            'allocated_days' => 'decimal:1',
            'carried_over'   => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Total days available for the year
    public function totalAllocated(): float
    {
        return (float) $this->allocated_days + (float) $this->carried_over;
    }

    // Days used = sum of approved requests for this user/type/year
    public function usedDays(): float
    {
        return (float) LeaveRequest::where('user_id', $this->user_id)
            ->where('leave_type_id', $this->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $this->year)
            ->sum('total_days');
    }

    // Days remaining
    public function remainingDays(): float
    {
        return max(0, $this->totalAllocated() - $this->usedDays());
    }
}
```

- [ ] **Step 3: Create LeaveRequest model**

```php
// app/Models/LeaveRequest.php
<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id', 'leave_type_id', 'start_date', 'end_date',
        'total_days', 'reason', 'status', 'manager_id',
        'manager_note', 'hr_notified_at', 'document_path',
    ];

    protected function casts(): array
    {
        return [
            'start_date'      => 'date',
            'end_date'        => 'date',
            'total_days'      => 'decimal:1',
            'hr_notified_at'  => 'datetime',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Count weekdays between start and end inclusive
    public static function countWeekdays(Carbon $start, Carbon $end): float
    {
        $days = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if ($current->isWeekday()) {
                $days++;
            }
            $current->addDay();
        }
        return (float) $days;
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function canBeCancelled(): bool { return $this->status === 'pending'; }
}
```

- [ ] **Step 4: Add leave relations to User model**

Add to `app/Models/User.php` after the `documents()` method:

```php
public function leaveBalances(): HasMany
{
    return $this->hasMany(LeaveBalance::class);
}

public function leaveRequests(): HasMany
{
    return $this->hasMany(LeaveRequest::class);
}

public function leaveApprovals(): HasMany
{
    return $this->hasMany(LeaveRequest::class, 'manager_id');
}
```

- [ ] **Step 5: Commit**

```bash
git add app/Models/Leave*.php app/Models/User.php
git commit -m "feat: leave models (type, balance, request) with business logic methods"
```

---

## Task 5: Unit Tests for Leave Business Logic

**Files:**
- Modify: `phpunit.xml`
- Create: `tests/Unit/LeaveBalanceTest.php`
- Create: `tests/Unit/LeaveRequestTest.php`

- [ ] **Step 1: Enable SQLite in-memory for tests**

In `phpunit.xml`, uncomment the SQLite lines:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

- [ ] **Step 2: Write failing test for LeaveBalance::remainingDays()**

```php
// tests/Unit/LeaveBalanceTest.php
<?php
namespace Tests\Unit;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveBalanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private LeaveType $leaveType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->leaveType = LeaveType::create([
            'name' => 'Annual Leave', 'code' => 'annual',
            'is_paid' => true, 'requires_document' => false, 'is_active' => true,
        ]);
    }

    public function test_remaining_days_with_no_requests(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 12.0,
            'carried_over'   => 2.0,
        ]);

        $this->assertEquals(14.0, $balance->remainingDays());
    }

    public function test_remaining_days_deducts_approved_requests(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 12.0,
            'carried_over'   => 0.0,
        ]);

        LeaveRequest::create([
            'user_id'       => $this->user->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => '2026-03-02',
            'end_date'      => '2026-03-04',
            'total_days'    => 3.0,
            'status'        => 'approved',
        ]);

        $this->assertEquals(9.0, $balance->remainingDays());
    }

    public function test_pending_requests_do_not_reduce_balance(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 12.0,
            'carried_over'   => 0.0,
        ]);

        LeaveRequest::create([
            'user_id'       => $this->user->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => '2026-03-02',
            'end_date'      => '2026-03-04',
            'total_days'    => 3.0,
            'status'        => 'pending',
        ]);

        $this->assertEquals(12.0, $balance->remainingDays());
    }

    public function test_remaining_days_never_negative(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 2.0,
            'carried_over'   => 0.0,
        ]);

        LeaveRequest::create([
            'user_id'       => $this->user->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => '2026-03-02',
            'end_date'      => '2026-03-06',
            'total_days'    => 5.0,
            'status'        => 'approved',
        ]);

        $this->assertEquals(0.0, $balance->remainingDays());
    }
}
```

- [ ] **Step 3: Run test — expect failure (User factory missing fields)**

```bash
php artisan test tests/Unit/LeaveBalanceTest.php --verbose
```

Expected: Tests fail or pass depending on factory setup. If User factory is missing `is_active`, add it:

In `database/factories/UserFactory.php`, ensure `is_active => true` is in `definition()`.

- [ ] **Step 4: Write failing test for LeaveRequest::countWeekdays()**

```php
// tests/Unit/LeaveRequestTest.php
<?php
namespace Tests\Unit;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    public function test_count_weekdays_single_day(): void
    {
        $monday = Carbon::parse('2026-03-02'); // Monday
        $this->assertEquals(1.0, LeaveRequest::countWeekdays($monday, $monday));
    }

    public function test_count_weekdays_full_week(): void
    {
        $monday = Carbon::parse('2026-03-02');
        $friday = Carbon::parse('2026-03-06');
        $this->assertEquals(5.0, LeaveRequest::countWeekdays($monday, $friday));
    }

    public function test_count_weekdays_excludes_weekend(): void
    {
        $friday = Carbon::parse('2026-03-06');
        $monday = Carbon::parse('2026-03-09');
        // Fri + Mon = 2 weekdays
        $this->assertEquals(2.0, LeaveRequest::countWeekdays($friday, $monday));
    }

    public function test_can_be_cancelled_when_pending(): void
    {
        $request = new LeaveRequest(['status' => 'pending']);
        $this->assertTrue($request->canBeCancelled());
    }

    public function test_cannot_be_cancelled_when_approved(): void
    {
        $request = new LeaveRequest(['status' => 'approved']);
        $this->assertFalse($request->canBeCancelled());
    }
}
```

- [ ] **Step 5: Run tests**

```bash
php artisan test tests/Unit/LeaveBalanceTest.php tests/Unit/LeaveRequestTest.php --verbose
```

Expected: All tests PASS.

- [ ] **Step 6: Commit**

```bash
git add tests/Unit/LeaveBalanceTest.php tests/Unit/LeaveRequestTest.php phpunit.xml
git commit -m "test: leave balance remaining days + request weekday count logic"
```

---

## Task 6: Seeders

**Files:**
- Create: `database/seeders/LeaveTypeSeeder.php`
- Create: `database/seeders/LeaveBalanceSampleSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Create LeaveTypeSeeder**

```php
// database/seeders/LeaveTypeSeeder.php
<?php
namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Annual Leave',       'code' => 'annual',        'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Medical Leave',       'code' => 'mc',            'is_paid' => true,  'requires_document' => true,  'max_days_per_year' => 22],
            ['name' => 'Emergency Leave',     'code' => 'emergency',     'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 3],
            ['name' => 'Maternity Leave',     'code' => 'maternity',     'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 98],
            ['name' => 'Paternity Leave',     'code' => 'paternity',     'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 7],
            ['name' => 'Unpaid Leave',        'code' => 'unpaid',        'is_paid' => false, 'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Replacement Leave',   'code' => 'replacement',   'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Study Leave',         'code' => 'study',         'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Compassionate Leave', 'code' => 'compassionate', 'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 3],
        ];

        foreach ($types as $type) {
            LeaveType::firstOrCreate(['code' => $type['code']], $type + ['is_active' => true]);
        }
    }
}
```

- [ ] **Step 2: Create LeaveBalanceSampleSeeder**

```php
// database/seeders/LeaveBalanceSampleSeeder.php
<?php
namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveBalanceSampleSeeder extends Seeder
{
    public function run(): void
    {
        $year = 2026;
        $annual = LeaveType::where('code', 'annual')->first();
        $mc = LeaveType::where('code', 'mc')->first();

        if (! $annual || ! $mc) return;

        // Seed annual + MC balance for first 5 active staff
        User::where('is_active', true)->take(5)->get()->each(function (User $user) use ($annual, $mc, $year) {
            LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $annual->id, 'year' => $year],
                ['allocated_days' => 12, 'carried_over' => 0]
            );
            LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $mc->id, 'year' => $year],
                ['allocated_days' => 14, 'carried_over' => 0]
            );
        });
    }
}
```

- [ ] **Step 3: Register seeders in DatabaseSeeder**

In `database/seeders/DatabaseSeeder.php`, add to the `run()` call list (after existing seeders):

```php
$this->call([
    // ... existing seeders ...
    LeaveTypeSeeder::class,
    LeaveBalanceSampleSeeder::class,
]);
```

- [ ] **Step 4: Run migrate:fresh --seed**

```bash
php artisan migrate:fresh --seed
```

Expected: No errors. All seeders complete.

- [ ] **Step 5: Verify leave types seeded**

```bash
php artisan tinker --execute="App\Models\LeaveType::all(['name','code'])->each(fn(\$t) => print(\"\$t->code: \$t->name\n\"));"
```

Expected: 9 leave types printed.

- [ ] **Step 6: Commit**

```bash
git add database/seeders/LeaveTypeSeeder.php database/seeders/LeaveBalanceSampleSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat: leave type and sample balance seeders"
```

---

## Task 7: EmployeeProfileResource (HR/Admin)

**Files:**
- Create: `app/Filament/Resources/EmployeeProfileResource.php`
- Create: `app/Filament/Resources/EmployeeProfileResource/Pages/ListEmployeeProfiles.php`
- Create: `app/Filament/Resources/EmployeeProfileResource/Pages/EditEmployeeProfile.php`
- Create: `app/Filament/Resources/EmployeeProfileResource/RelationManagers/EmergencyContactsRelationManager.php`
- Create: `app/Filament/Resources/EmployeeProfileResource/RelationManagers/EducationRelationManager.php`

- [ ] **Step 1: Create ListEmployeeProfiles page**

```php
// app/Filament/Resources/EmployeeProfileResource/Pages/ListEmployeeProfiles.php
<?php
namespace App\Filament\Resources\EmployeeProfileResource\Pages;

use App\Filament\Resources\EmployeeProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeProfiles extends ListRecords
{
    protected static string $resource = EmployeeProfileResource::class;
}
```

- [ ] **Step 2: Create EditEmployeeProfile page**

```php
// app/Filament/Resources/EmployeeProfileResource/Pages/EditEmployeeProfile.php
<?php
namespace App\Filament\Resources\EmployeeProfileResource\Pages;

use App\Filament\Resources\EmployeeProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeProfile extends EditRecord
{
    protected static string $resource = EmployeeProfileResource::class;

    protected function getHeaderActions(): array { return []; }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load(['employeeProfile', 'bankDetail']);

        $data['employeeProfile'] = $record->employeeProfile?->toArray() ?? [];
        $data['bankDetail']      = $record->bankDetail?->toArray() ?? [];

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $profileData = $data['employeeProfile'] ?? [];
        $bankData    = $data['bankDetail'] ?? [];
        unset($data['employeeProfile'], $data['bankDetail']);

        $record->update($data);

        if ($profileData) {
            $record->employeeProfile()->updateOrCreate(['user_id' => $record->id], $profileData);
        }
        if ($bankData) {
            $record->bankDetail()->updateOrCreate(['user_id' => $record->id], $bankData);
        }

        return $record;
    }
}
```

- [ ] **Step 3: Create EmergencyContactsRelationManager**

```php
// app/Filament/Resources/EmployeeProfileResource/RelationManagers/EmergencyContactsRelationManager.php
<?php
namespace App\Filament\Resources\EmployeeProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmergencyContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'emergencyContacts';
    protected static ?string $title = 'Emergency Contacts';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(200),
            Forms\Components\TextInput::make('relationship')->required()->maxLength(100),
            Forms\Components\TextInput::make('phone')->required()->tel()->maxLength(20),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('relationship'),
            Tables\Columns\TextColumn::make('phone'),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
```

- [ ] **Step 4: Create EducationRelationManager**

```php
// app/Filament/Resources/EmployeeProfileResource/RelationManagers/EducationRelationManager.php
<?php
namespace App\Filament\Resources\EmployeeProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EducationRelationManager extends RelationManager
{
    protected static string $relationship = 'education';
    protected static ?string $title = 'Education History';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('institution')->required()->maxLength(300)->columnSpanFull(),
            Forms\Components\Select::make('qualification')
                ->options(['SPM' => 'SPM', 'Diploma' => 'Diploma', 'Degree' => 'Degree',
                           'Masters' => 'Masters', 'PhD' => 'PhD', 'Other' => 'Other'])
                ->required(),
            Forms\Components\TextInput::make('field_of_study')->maxLength(200),
            Forms\Components\TextInput::make('year_completed')->numeric()->minValue(1970)->maxValue(date('Y')),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('qualification')->badge(),
            Tables\Columns\TextColumn::make('institution'),
            Tables\Columns\TextColumn::make('field_of_study'),
            Tables\Columns\TextColumn::make('year_completed'),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
```

- [ ] **Step 5: Create EmployeeProfileResource**

```php
// app/Filament/Resources/EmployeeProfileResource.php
<?php
namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeProfileResource\Pages;
use App\Filament\Resources\EmployeeProfileResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeProfileResource extends Resource
{
    protected static ?string $model           = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'People';
    protected static ?string $navigationLabel = 'Employee Profiles';
    protected static ?string $modelLabel      = 'Employee';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $slug            = 'employee-profiles';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Profile')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Personal')
                        ->schema([
                            Forms\Components\TextInput::make('name')->disabled(),
                            Forms\Components\TextInput::make('job_title')->disabled(),
                            Forms\Components\DatePicker::make('employeeProfile.date_of_birth')->label('Date of Birth'),
                            Forms\Components\Select::make('employeeProfile.gender')
                                ->label('Gender')
                                ->options(['male' => 'Male', 'female' => 'Female']),
                            Forms\Components\Select::make('employeeProfile.marital_status')
                                ->label('Marital Status')
                                ->options(['single' => 'Single', 'married' => 'Married',
                                           'divorced' => 'Divorced', 'widowed' => 'Widowed']),
                            Forms\Components\TextInput::make('employeeProfile.nationality')
                                ->label('Nationality')->default('Malaysian'),
                            Forms\Components\Textarea::make('employeeProfile.address')
                                ->label('Address')->columnSpanFull()->rows(2),
                            Forms\Components\TextInput::make('employeeProfile.city')->label('City'),
                            Forms\Components\TextInput::make('employeeProfile.state')->label('State'),
                            Forms\Components\TextInput::make('employeeProfile.postcode')->label('Postcode'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Bank')
                        ->schema([
                            Forms\Components\TextInput::make('bankDetail.bank_name')->label('Bank Name'),
                            Forms\Components\TextInput::make('bankDetail.account_holder_name')->label('Account Holder Name'),
                            Forms\Components\TextInput::make('bankDetail.account_number')
                                ->label('Account Number')
                                ->password()
                                ->revealable(),
                        ])->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('job_title')->searchable(),
                Tables\Columns\TextColumn::make('unit.name')->label('Unit'),
                Tables\Columns\TextColumn::make('employeeProfile.date_of_birth')
                    ->label('DOB')->date()->toggleable(),
                Tables\Columns\TextColumn::make('join_date')->date()->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'warning' => 'on_leave', 'danger' => 'inactive']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'on_leave' => 'On Leave']),
            ])
            ->defaultSort('name');
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\EmergencyContactsRelationManager::class,
            RelationManagers\EducationRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeProfiles::route('/'),
            'edit'  => Pages\EditEmployeeProfile::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 6: Test in browser — navigate to /app/employee-profiles**

```bash
php artisan serve --port=8001
# Open: http://localhost:8001/app/employee-profiles
# Login as shahril@unijaya.com.my / password
```

Expected: Employee Profiles list loads, shows staff. Click Edit on a staff member — see Personal and Bank tabs.

- [ ] **Step 7: Commit**

```bash
git add app/Filament/Resources/EmployeeProfileResource.php app/Filament/Resources/EmployeeProfileResource/
git commit -m "feat: EmployeeProfileResource with personal + bank tabs and relation managers"
```

---

## Task 8: Document Uploads RelationManager

**Files:**
- Create: `app/Filament/Resources/EmployeeProfileResource/RelationManagers/DocumentsRelationManager.php`

- [ ] **Step 1: Create DocumentsRelationManager**

```php
// app/Filament/Resources/EmployeeProfileResource/RelationManagers/DocumentsRelationManager.php
<?php
namespace App\Filament\Resources\EmployeeProfileResource\RelationManagers;

use App\Models\EmployeeDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('category')
                ->options(EmployeeDocument::CATEGORIES)
                ->required()
                ->live(),
            Forms\Components\TextInput::make('label')
                ->label('Document name')
                ->visible(fn (Forms\Get $get) => $get('category') === 'other')
                ->required(fn (Forms\Get $get) => $get('category') === 'other')
                ->maxLength(200),
            Forms\Components\FileUpload::make('file_path')
                ->label('File')
                ->disk('local')
                ->directory(fn ($record) => 'documents/' . ($record?->id ?? $this->getOwnerRecord()->id))
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->maxSize(10240) // 10 MB
                ->required()
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('display_label')->label('Document'),
            Tables\Columns\TextColumn::make('category')->badge(),
            Tables\Columns\TextColumn::make('uploader.name')->label('Uploaded by'),
            Tables\Columns\TextColumn::make('created_at')->label('Date')->date(),
        ])->headerActions([
            Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['uploaded_by'] = auth()->id();
                    return $data;
                }),
        ])->actions([
            Tables\Actions\Action::make('download')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('filament.app.pages.document-download', ['id' => $record->id]))
                ->openUrlInNewTab(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
```

- [ ] **Step 2: Register DocumentsRelationManager in EmployeeProfileResource**

In `app/Filament/Resources/EmployeeProfileResource.php`, update `getRelationManagers()`:

```php
public static function getRelationManagers(): array
{
    return [
        RelationManagers\EmergencyContactsRelationManager::class,
        RelationManagers\EducationRelationManager::class,
        RelationManagers\DocumentsRelationManager::class,
    ];
}
```

- [ ] **Step 3: Add document download route**

In `routes/web.php`, add before the last line:

```php
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/documents/download/{id}', function ($id) {
        $doc = \App\Models\EmployeeDocument::findOrFail($id);
        $user = auth()->user();

        // Staff can only download own documents
        if (! $user->hasAnyRole(['admin', 'hr']) && $doc->user_id !== $user->id) {
            abort(403);
        }

        if (! \Storage::disk('local')->exists($doc->file_path)) {
            abort(404);
        }

        return \Storage::disk('local')->download($doc->file_path, $doc->display_label);
    })->name('filament.app.pages.document-download');
});
```

- [ ] **Step 4: Test document upload in browser**

Open `/app/employee-profiles`, edit any staff, go to Documents tab. Upload a PDF. Verify row appears. Click download — file should download.

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Resources/EmployeeProfileResource/RelationManagers/DocumentsRelationManager.php \
        app/Filament/Resources/EmployeeProfileResource.php \
        routes/web.php
git commit -m "feat: document upload + download for employee profiles"
```

---

## Task 9: LeaveTypeResource

**Files:**
- Create: `app/Filament/Resources/LeaveTypeResource.php`
- Create: `app/Filament/Resources/LeaveTypeResource/Pages/ListLeaveTypes.php`
- Create: `app/Filament/Resources/LeaveTypeResource/Pages/CreateLeaveType.php`
- Create: `app/Filament/Resources/LeaveTypeResource/Pages/EditLeaveType.php`

- [ ] **Step 1: Create page classes**

```php
// app/Filament/Resources/LeaveTypeResource/Pages/ListLeaveTypes.php
<?php
namespace App\Filament\Resources\LeaveTypeResource\Pages;
use App\Filament\Resources\LeaveTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListLeaveTypes extends ListRecords {
    protected static string $resource = LeaveTypeResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
```

```php
// app/Filament/Resources/LeaveTypeResource/Pages/CreateLeaveType.php
<?php
namespace App\Filament\Resources\LeaveTypeResource\Pages;
use App\Filament\Resources\LeaveTypeResource;
use Filament\Resources\Pages\CreateRecord;
class CreateLeaveType extends CreateRecord {
    protected static string $resource = LeaveTypeResource::class;
}
```

```php
// app/Filament/Resources/LeaveTypeResource/Pages/EditLeaveType.php
<?php
namespace App\Filament\Resources\LeaveTypeResource\Pages;
use App\Filament\Resources\LeaveTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditLeaveType extends EditRecord {
    protected static string $resource = LeaveTypeResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
}
```

- [ ] **Step 2: Create LeaveTypeResource**

```php
// app/Filament/Resources/LeaveTypeResource.php
<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LeaveTypeResource\Pages;
use App\Models\LeaveType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveTypeResource extends Resource
{
    protected static ?string $model           = LeaveType::class;
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Leave';
    protected static ?string $navigationLabel = 'Leave Types';
    protected static ?int    $navigationSort  = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(100),
            Forms\Components\TextInput::make('code')->required()->unique(ignoreRecord: true)->maxLength(50)
                ->helperText('Lowercase, no spaces. e.g. annual, mc'),
            Forms\Components\Toggle::make('is_paid')->label('Paid leave')->default(true),
            Forms\Components\Toggle::make('requires_document')->label('Requires document (e.g. MC cert)'),
            Forms\Components\TextInput::make('max_days_per_year')
                ->numeric()->nullable()->minValue(0)
                ->helperText('Leave blank for unlimited'),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable(),
            Tables\Columns\TextColumn::make('code')->badge(),
            Tables\Columns\IconColumn::make('is_paid')->boolean()->label('Paid'),
            Tables\Columns\IconColumn::make('requires_document')->boolean()->label('Doc Required'),
            Tables\Columns\TextColumn::make('max_days_per_year')->label('Max Days/Yr')
                ->formatStateUsing(fn ($state) => $state ? $state : '—'),
            Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'edit'   => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Browser verify**

Open `/app/leave-types` — 9 seeded types visible.

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/LeaveTypeResource.php app/Filament/Resources/LeaveTypeResource/
git commit -m "feat: LeaveTypeResource (admin only)"
```

---

## Task 10: LeaveBalanceResource

**Files:**
- Create: `app/Filament/Resources/LeaveBalanceResource.php` + Pages/

- [ ] **Step 1: Create page classes**

```php
// app/Filament/Resources/LeaveBalanceResource/Pages/ListLeaveBalances.php
<?php
namespace App\Filament\Resources\LeaveBalanceResource\Pages;
use App\Filament\Resources\LeaveBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListLeaveBalances extends ListRecords {
    protected static string $resource = LeaveBalanceResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
```

```php
// app/Filament/Resources/LeaveBalanceResource/Pages/CreateLeaveBalance.php
<?php
namespace App\Filament\Resources\LeaveBalanceResource\Pages;
use App\Filament\Resources\LeaveBalanceResource;
use Filament\Resources\Pages\CreateRecord;
class CreateLeaveBalance extends CreateRecord {
    protected static string $resource = LeaveBalanceResource::class;
}
```

```php
// app/Filament/Resources/LeaveBalanceResource/Pages/EditLeaveBalance.php
<?php
namespace App\Filament\Resources\LeaveBalanceResource\Pages;
use App\Filament\Resources\LeaveBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditLeaveBalance extends EditRecord {
    protected static string $resource = LeaveBalanceResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
}
```

- [ ] **Step 2: Create LeaveBalanceResource**

```php
// app/Filament/Resources/LeaveBalanceResource.php
<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LeaveBalanceResource\Pages;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveBalanceResource extends Resource
{
    protected static ?string $model           = LeaveBalance::class;
    protected static ?string $navigationIcon  = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Leave';
    protected static ?string $navigationLabel = 'Leave Balances';
    protected static ?int    $navigationSort  = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Staff')
                ->options(User::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                ->searchable()->required(),
            Forms\Components\Select::make('leave_type_id')
                ->label('Leave Type')
                ->options(LeaveType::where('is_active', true)->pluck('name', 'id'))
                ->required(),
            Forms\Components\TextInput::make('year')
                ->numeric()->required()->default(date('Y'))
                ->minValue(2020)->maxValue(2030),
            Forms\Components\TextInput::make('allocated_days')
                ->numeric()->required()->minValue(0)->step(0.5),
            Forms\Components\TextInput::make('carried_over')
                ->numeric()->default(0)->minValue(0)->step(0.5),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Staff')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('leaveType.name')->label('Leave Type'),
            Tables\Columns\TextColumn::make('year')->sortable(),
            Tables\Columns\TextColumn::make('allocated_days')->label('Allocated'),
            Tables\Columns\TextColumn::make('carried_over')->label('C/F'),
            Tables\Columns\TextColumn::make('used_days_display')
                ->label('Used')
                ->getStateUsing(fn ($record) => $record->usedDays()),
            Tables\Columns\TextColumn::make('remaining_days_display')
                ->label('Remaining')
                ->getStateUsing(fn ($record) => $record->remainingDays()),
        ])->filters([
            Tables\Filters\SelectFilter::make('year')
                ->options(array_combine(range(2024, 2028), range(2024, 2028)))
                ->default(date('Y')),
            Tables\Filters\SelectFilter::make('leave_type_id')
                ->label('Leave Type')
                ->relationship('leaveType', 'name'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('user.name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveBalances::route('/'),
            'create' => Pages\CreateLeaveBalance::route('/create'),
            'edit'   => Pages\EditLeaveBalance::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Browser verify**

Open `/app/leave-balances` — sample balances visible with Used/Remaining columns.

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/LeaveBalanceResource.php app/Filament/Resources/LeaveBalanceResource/
git commit -m "feat: LeaveBalanceResource with live used/remaining columns"
```

---

## Task 11: LeaveRequestResource (HR/Admin View)

**Files:**
- Create: `app/Filament/Resources/LeaveRequestResource.php` + Pages/

- [ ] **Step 1: Create page classes**

```php
// app/Filament/Resources/LeaveRequestResource/Pages/ListLeaveRequests.php
<?php
namespace App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\ListRecords;
class ListLeaveRequests extends ListRecords {
    protected static string $resource = LeaveRequestResource::class;
}
```

```php
// app/Filament/Resources/LeaveRequestResource/Pages/ViewLeaveRequest.php
<?php
namespace App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\ViewRecord;
class ViewLeaveRequest extends ViewRecord {
    protected static string $resource = LeaveRequestResource::class;
}
```

- [ ] **Step 2: Create LeaveRequestResource**

```php
// app/Filament/Resources/LeaveRequestResource.php
<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model           = LeaveRequest::class;
    protected static ?string $navigationIcon  = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationGroup = 'Leave';
    protected static ?string $navigationLabel = 'All Leave Requests';
    protected static ?int    $navigationSort  = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function canCreate(): bool { return false; }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Request')->schema([
                Infolists\Components\TextEntry::make('applicant.name')->label('Staff'),
                Infolists\Components\TextEntry::make('leaveType.name')->label('Leave Type'),
                Infolists\Components\TextEntry::make('start_date')->date(),
                Infolists\Components\TextEntry::make('end_date')->date(),
                Infolists\Components\TextEntry::make('total_days')->label('Days'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'approved' => 'success', 'rejected' => 'danger',
                        'cancelled' => 'gray', default => 'warning',
                    }),
                Infolists\Components\TextEntry::make('reason')->columnSpanFull(),
            ])->columns(3),

            Infolists\Components\Section::make('Decision')
                ->hidden(fn ($record) => $record->status === 'pending')
                ->schema([
                    Infolists\Components\TextEntry::make('manager.name')->label('Decided by'),
                    Infolists\Components\TextEntry::make('manager_note')->label('Note')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('applicant.name')->label('Staff')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('leaveType.name')->label('Type'),
            Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('end_date')->date(),
            Tables\Columns\TextColumn::make('total_days')->label('Days'),
            Tables\Columns\TextColumn::make('status')->badge()
                ->color(fn ($state) => match($state) {
                    'approved' => 'success', 'rejected' => 'danger',
                    'cancelled' => 'gray', default => 'warning',
                }),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options(['pending' => 'Pending', 'approved' => 'Approved',
                           'rejected' => 'Rejected', 'cancelled' => 'Cancelled']),
            Tables\Filters\SelectFilter::make('leave_type_id')
                ->label('Leave Type')->relationship('leaveType', 'name'),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('approve')
                ->icon('heroicon-o-check-circle')->color('success')
                ->visible(fn ($record) => $record->isPending())
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'status'          => 'approved',
                        'manager_id'      => auth()->id(),
                        'hr_notified_at'  => now(),
                    ]);
                }),
            Tables\Actions\Action::make('reject')
                ->icon('heroicon-o-x-circle')->color('danger')
                ->visible(fn ($record) => $record->isPending())
                ->form([Forms\Components\Textarea::make('manager_note')->label('Reason')->required()])
                ->action(function ($record, array $data) {
                    $record->update([
                        'status'       => 'rejected',
                        'manager_id'   => auth()->id(),
                        'manager_note' => $data['manager_note'],
                    ]);
                }),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'view'  => Pages\ViewLeaveRequest::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Resources/LeaveRequestResource.php app/Filament/Resources/LeaveRequestResource/
git commit -m "feat: LeaveRequestResource — HR/admin view all requests + approve/reject"
```

---

## Task 12: MyLeavePage (Staff)

**Files:**
- Create: `app/Filament/Pages/MyLeavePage.php`
- Create: `resources/views/filament/pages/my-leave.blade.php`

- [ ] **Step 1: Create MyLeavePage**

```php
// app/Filament/Pages/MyLeavePage.php
<?php
namespace App\Filament\Pages;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MyLeavePage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'My Leave';
    protected static ?string $navigationGroup = 'My Account';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.my-leave';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Select::make('leave_type_id')
                ->label('Leave Type')
                ->options(LeaveType::active()->pluck('name', 'id'))
                ->required()
                ->live(),
            Forms\Components\DatePicker::make('start_date')
                ->label('From')->required()->native(false)
                ->minDate(now()),
            Forms\Components\DatePicker::make('end_date')
                ->label('To')->required()->native(false)
                ->minDate(fn (Forms\Get $get) => $get('start_date') ?? now()),
            Forms\Components\Textarea::make('reason')
                ->label('Reason')->rows(3)->columnSpanFull(),
            Forms\Components\FileUpload::make('document_path')
                ->label('Supporting Document (required for MC)')
                ->disk('local')
                ->directory('leave-documents/' . auth()->id())
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->maxSize(10240)
                ->visible(fn (Forms\Get $get) => LeaveType::find($get('leave_type_id'))?->requires_document)
                ->required(fn (Forms\Get $get) => LeaveType::find($get('leave_type_id'))?->requires_document)
                ->columnSpanFull(),
        ])->columns(3);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('apply')
                ->label('Apply for Leave')
                ->submit('apply'),
        ];
    }

    public function apply(): void
    {
        $data = $this->form->getState();

        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);
        $totalDays = LeaveRequest::countWeekdays($start, $end);

        LeaveRequest::create([
            'user_id'       => auth()->id(),
            'leave_type_id' => $data['leave_type_id'],
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'total_days'    => $totalDays,
            'reason'        => $data['reason'] ?? null,
            'document_path' => $data['document_path'] ?? null,
            'status'        => 'pending',
        ]);

        // Notify manager
        $manager = auth()->user()->superior;
        if ($manager) {
            Notification::make()
                ->title('Leave request from ' . auth()->user()->name)
                ->body($totalDays . ' day(s) — ' . LeaveType::find($data['leave_type_id'])?->name)
                ->sendToDatabase($manager);
        }

        Notification::make()->title('Leave application submitted.')->success()->send();
        $this->form->fill();
    }

    public function cancelRequest(int $id): void
    {
        $request = LeaveRequest::where('user_id', auth()->id())->findOrFail($id);

        if (! $request->canBeCancelled()) {
            Notification::make()->title('Cannot cancel an already-decided request.')->danger()->send();
            return;
        }

        $request->update(['status' => 'cancelled']);
        Notification::make()->title('Request cancelled.')->success()->send();
    }

    public function getBalances(): \Illuminate\Support\Collection
    {
        return LeaveBalance::where('user_id', auth()->id())
            ->where('year', date('Y'))
            ->with('leaveType')
            ->get();
    }

    public function getMyRequests(): \Illuminate\Support\Collection
    {
        return LeaveRequest::where('user_id', auth()->id())
            ->with('leaveType')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
    }
}
```

- [ ] **Step 2: Create Blade view**

```blade
{{-- resources/views/filament/pages/my-leave.blade.php --}}
<x-filament-panels::page>
    {{-- Balance summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach($this->getBalances() as $balance)
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $balance->leaveType->name }}</p>
            <p class="text-2xl font-bold text-amber-600">{{ number_format($balance->remainingDays(), 1) }}</p>
            <p class="text-xs text-gray-400">of {{ number_format($balance->totalAllocated(), 1) }} days</p>
        </div>
        @endforeach
    </div>

    {{-- Apply form --}}
    <x-filament::section heading="Apply for Leave">
        <x-filament-panels::form wire:submit="apply">
            {{ $this->form }}
            <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" />
        </x-filament-panels::form>
    </x-filament::section>

    {{-- History --}}
    <x-filament::section heading="My Leave History" class="mt-6">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b dark:border-gray-700">
                    <th class="pb-2">Type</th>
                    <th class="pb-2">From</th>
                    <th class="pb-2">To</th>
                    <th class="pb-2">Days</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->getMyRequests() as $req)
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2">{{ $req->leaveType->name }}</td>
                    <td class="py-2">{{ $req->start_date->format('d M Y') }}</td>
                    <td class="py-2">{{ $req->end_date->format('d M Y') }}</td>
                    <td class="py-2">{{ $req->total_days }}</td>
                    <td class="py-2">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $req->status === 'approved' ? 'bg-green-100 text-green-700' :
                               ($req->status === 'rejected' ? 'bg-red-100 text-red-700' :
                               ($req->status === 'cancelled' ? 'bg-gray-100 text-gray-500' :
                               'bg-amber-100 text-amber-700')) }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </td>
                    <td class="py-2">
                        @if($req->canBeCancelled())
                        <button wire:click="cancelRequest({{ $req->id }})"
                                class="text-xs text-red-500 hover:underline"
                                wire:confirm="Cancel this leave request?">
                            Cancel
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-4 text-gray-400 text-center">No leave requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-filament::section>
</x-filament-panels::page>
```

- [ ] **Step 3: Browser verify**

Open `/app/my-leave`. Balances should show for seeded staff. Apply for a leave — form submits, request appears in history as `pending`.

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Pages/MyLeavePage.php resources/views/filament/pages/my-leave.blade.php
git commit -m "feat: MyLeavePage — staff apply leave, view balance, cancel pending requests"
```

---

## Task 13: LeaveApprovalsPage (Manager)

**Files:**
- Create: `app/Filament/Pages/LeaveApprovalsPage.php`
- Create: `resources/views/filament/pages/leave-approvals.blade.php`

- [ ] **Step 1: Create LeaveApprovalsPage**

```php
// app/Filament/Pages/LeaveApprovalsPage.php
<?php
namespace App\Filament\Pages;

use App\Models\LeaveRequest;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LeaveApprovalsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Leave Approvals';
    protected static ?string $navigationGroup = 'My Account';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.leave-approvals';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr', 'manager']) ?? false;
    }

    public function getPendingRequests(): \Illuminate\Support\Collection
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'hr'])) {
            return LeaveRequest::where('status', 'pending')
                ->with(['applicant', 'leaveType'])->get();
        }

        // Manager: only direct reports
        $subordinateIds = $user->subordinates()->pluck('id');
        return LeaveRequest::where('status', 'pending')
            ->whereIn('user_id', $subordinateIds)
            ->with(['applicant', 'leaveType'])->get();
    }

    public function approve(int $id): void
    {
        $request = $this->authorizeRequest($id);

        $request->update([
            'status'         => 'approved',
            'manager_id'     => auth()->id(),
            'hr_notified_at' => now(),
        ]);

        Notification::make()
            ->title('Leave approved')
            ->body($request->applicant->name . ' — ' . $request->leaveType->name)
            ->success()->send();

        // Notify applicant
        Notification::make()
            ->title('Your leave request was approved')
            ->success()
            ->sendToDatabase($request->applicant);
    }

    public function reject(int $id, string $note = ''): void
    {
        $request = $this->authorizeRequest($id);

        $request->update([
            'status'       => 'rejected',
            'manager_id'   => auth()->id(),
            'manager_note' => $note,
        ]);

        Notification::make()
            ->title('Leave rejected')
            ->body($request->applicant->name)
            ->danger()->send();

        Notification::make()
            ->title('Your leave request was not approved')
            ->body($note ?: 'No reason provided.')
            ->danger()
            ->sendToDatabase($request->applicant);
    }

    private function authorizeRequest(int $id): LeaveRequest
    {
        $user = auth()->user();
        $request = LeaveRequest::with(['applicant', 'leaveType'])->findOrFail($id);

        if ($user->hasAnyRole(['admin', 'hr'])) return $request;

        $subordinateIds = $user->subordinates()->pluck('id')->toArray();
        if (! in_array($request->user_id, $subordinateIds)) abort(403);

        return $request;
    }
}
```

- [ ] **Step 2: Create Blade view**

```blade
{{-- resources/views/filament/pages/leave-approvals.blade.php --}}
<x-filament-panels::page>
    @php $pending = $this->getPendingRequests() @endphp

    @if($pending->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <x-heroicon-o-check-circle class="w-12 h-12 mx-auto mb-2 text-green-400" />
            <p>No pending leave requests.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($pending as $req)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 flex items-start justify-between gap-4">
                <div>
                    <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $req->applicant->name }}</p>
                    <p class="text-sm text-gray-500">{{ $req->leaveType->name }} — {{ $req->total_days }} day(s)</p>
                    <p class="text-sm text-gray-400">{{ $req->start_date->format('d M') }} – {{ $req->end_date->format('d M Y') }}</p>
                    @if($req->reason)
                    <p class="text-sm text-gray-500 mt-1 italic">{{ $req->reason }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">Applied {{ $req->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex flex-col gap-2 min-w-fit">
                    <button wire:click="approve({{ $req->id }})"
                            wire:confirm="Approve this request?"
                            class="px-4 py-1.5 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                        Approve
                    </button>
                    <button
                        x-data="{ note: '' }"
                        x-on:click="note = prompt('Rejection reason (optional):') ?? ''; if (note !== null) $wire.reject({{ $req->id }}, note)"
                        class="px-4 py-1.5 rounded-lg bg-red-100 text-red-700 text-sm font-medium hover:bg-red-200">
                        Reject
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
```

- [ ] **Step 3: Browser verify**

Login as a manager (e.g. Haryati). Open `/app/leave-approvals`. Submit a leave request as a subordinate, then verify it appears. Approve it — verify status changes.

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Pages/LeaveApprovalsPage.php resources/views/filament/pages/leave-approvals.blade.php
git commit -m "feat: LeaveApprovalsPage — manager approve/reject with notifications"
```

---

## Task 14: Enhance MyProfile (Staff Self-Service Profile)

**Files:**
- Modify: `app/Filament/Pages/MyProfile.php`

- [ ] **Step 1: Read current MyProfile.php**

```bash
cat app/Filament/Pages/MyProfile.php
```

Note current structure before editing.

- [ ] **Step 2: Add profile editing to MyProfile**

Replace the `form()` method content to include personal + bank + emergency sections. Add a save action that creates/updates the related records:

```php
public function form(Form $form): Form
{
    return $form->statePath('data')->schema([
        Forms\Components\Tabs::make('My Profile')
            ->tabs([
                Forms\Components\Tabs\Tab::make('Personal Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')->disabled(),
                        Forms\Components\TextInput::make('email')->disabled(),
                        Forms\Components\DatePicker::make('profile.date_of_birth')->label('Date of Birth'),
                        Forms\Components\Select::make('profile.gender')->label('Gender')
                            ->options(['male' => 'Male', 'female' => 'Female']),
                        Forms\Components\Select::make('profile.marital_status')->label('Marital Status')
                            ->options(['single' => 'Single', 'married' => 'Married',
                                       'divorced' => 'Divorced', 'widowed' => 'Widowed']),
                        Forms\Components\TextInput::make('profile.nationality')->label('Nationality'),
                        Forms\Components\Textarea::make('profile.address')->label('Address')
                            ->columnSpanFull()->rows(2),
                        Forms\Components\TextInput::make('profile.city')->label('City'),
                        Forms\Components\TextInput::make('profile.state')->label('State'),
                        Forms\Components\TextInput::make('profile.postcode')->label('Postcode'),
                    ])->columns(2),

                Forms\Components\Tabs\Tab::make('Documents')
                    ->schema([
                        Forms\Components\Placeholder::make('docs_note')
                            ->content('Upload your documents below. HR will be notified.'),
                        Forms\Components\Repeater::make('new_documents')
                            ->label('Upload Documents')
                            ->schema([
                                Forms\Components\Select::make('category')
                                    ->options(\App\Models\EmployeeDocument::CATEGORIES)
                                    ->required()->live(),
                                Forms\Components\TextInput::make('label')
                                    ->visible(fn (Forms\Get $get) => $get('category') === 'other')
                                    ->required(fn (Forms\Get $get) => $get('category') === 'other'),
                                Forms\Components\FileUpload::make('file_path')
                                    ->disk('local')
                                    ->directory('documents/' . auth()->id())
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(10240)->required()->columnSpanFull(),
                            ])->columns(2)->defaultItems(0),
                    ]),
            ])->columnSpanFull(),
    ]);
}
```

- [ ] **Step 3: Update mount() to prefill profile data**

```php
public function mount(): void
{
    $user = auth()->user()->load('employeeProfile');
    $this->form->fill([
        'name'    => $user->name,
        'email'   => $user->email,
        'profile' => $user->employeeProfile?->toArray() ?? [],
    ]);
}
```

- [ ] **Step 4: Update save action to persist profile + documents**

```php
public function save(): void
{
    $data = $this->form->getState();
    $user = auth()->user();

    // Save profile
    if (! empty($data['profile'])) {
        $user->employeeProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $data['profile']
        );
    }

    // Save documents
    foreach ($data['new_documents'] ?? [] as $doc) {
        \App\Models\EmployeeDocument::create([
            'user_id'     => $user->id,
            'category'    => $doc['category'],
            'label'       => $doc['label'] ?? null,
            'file_path'   => $doc['file_path'],
            'uploaded_by' => $user->id,
        ]);
    }

    \Filament\Notifications\Notification::make()
        ->title('Profile updated.')->success()->send();
}
```

- [ ] **Step 5: Browser verify**

Login as staff. Open `/app/my-profile`. Fill in DOB, gender, address. Upload a document. Save. Reload — data persists.

- [ ] **Step 6: Commit**

```bash
git add app/Filament/Pages/MyProfile.php
git commit -m "feat: MyProfile — staff can edit personal info and upload own documents"
```

---

## Task 15: User Factory Check + Full Regression Test

**Files:** None new

- [ ] **Step 1: Ensure User factory has required fields**

Check `database/factories/UserFactory.php` — ensure `is_active` defaults to `true`. If missing, add:

```php
'is_active' => true,
'join_date' => fake()->dateTimeBetween('-5 years', 'now'),
```

- [ ] **Step 2: Run full test suite**

```bash
php artisan test --verbose
```

Expected: All tests PASS (2 unit test classes + any existing tests).

- [ ] **Step 3: Run migrate:fresh --seed**

```bash
php artisan migrate:fresh --seed
```

Expected: No errors.

- [ ] **Step 4: End-to-end browser walkthrough**

Login as shahril (admin) at `http://localhost:8001/app`:

1. `/app/employee-profiles` — list loads, edit a staff, fill personal + bank tab, save
2. `/app/employee-profiles/{id}/edit` — Documents tab — upload a PDF
3. `/app/leave-types` — 9 types listed
4. `/app/leave-balances` — balances shown with Used/Remaining
5. `/app/leave-requests` — empty or seeded requests

Login as a staff user (e.g. nurin@unijaya.com.my / password):

6. `/app/my-leave` — balance cards show, apply for 2 days annual leave
7. Request shows as `pending` in history

Login as manager (e.g. haryati@unijaya.com.my / password):

8. `/app/leave-approvals` — Nurin's request appears, approve it
9. Login back as nurin — request shows `approved`

- [ ] **Step 5: Final commit**

```bash
git add -A
git commit -m "feat: leave management + employee profile — full feature complete"
```

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RoleRefactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    /** @test */
    public function only_admin_and_parent_roles_exist()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $parent = User::factory()->create(['role' => UserRole::PARENT]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isParent());
        
        $this->assertTrue($parent->isParent());
        $this->assertFalse($parent->isAdmin());
    }

    /** @test */
    public function admin_can_access_admin_panel()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->assertTrue($admin->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')));
    }

    /** @test */
    public function parent_can_access_parent_panel()
    {
        $parent = User::factory()->create(['role' => UserRole::PARENT]);

        $this->assertTrue($parent->canAccessPanel(\Filament\Facades\Filament::getPanel('parent')));
    }

    /** @test */
    public function parent_can_only_see_their_own_students()
    {
        // Create parent user with guardian
        $parent = User::factory()->create(['role' => UserRole::PARENT]);
        $guardian = Guardian::factory()->create(['user_id' => $parent->id]);
        
        // Create students
        $ownStudent = Student::factory()->create();
        $otherStudent = Student::factory()->create();
        
        // Link guardian to their student
        $guardian->students()->attach($ownStudent->id, ['is_primary' => true]);

        // Act as parent
        $this->actingAs($parent);

        // Parent should only see their own student due to ParentStudentScope
        $students = Student::all();
        
        $this->assertTrue($students->contains($ownStudent->id));
        $this->assertFalse($students->contains($otherStudent->id));
    }

    /** @test */
    public function admin_can_see_all_students()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $this->actingAs($admin);

        $students = Student::all();
        
        $this->assertCount(2, $students);
        $this->assertTrue($students->contains($student1->id));
        $this->assertTrue($students->contains($student2->id));
    }

    /** @test */
    public function parent_cannot_create_students()
    {
        $parent = User::factory()->create(['role' => UserRole::PARENT]);
        $student = Student::factory()->make();

        $this->actingAs($parent);

        $this->assertFalse($parent->can('create', Student::class));
    }

    /** @test */
    public function parent_cannot_edit_students()
    {
        $parent = User::factory()->create(['role' => UserRole::PARENT]);
        $student = Student::factory()->create();

        $this->actingAs($parent);

        $this->assertFalse($parent->can('update', $student));
    }

    /** @test */
    public function admin_can_create_and_edit_students()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $student = Student::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('create', Student::class));
        $this->assertTrue($admin->can('update', $student));
    }

    /** @test */
    public function parent_redirected_from_admin_panel()
    {
        $parent = User::factory()->create(['role' => UserRole::PARENT]);

        $response = $this->actingAs($parent)->get('/admin');

        $response->assertRedirect('/parent');
    }

    /** @test */
    public function guardian_relationship_works()
    {
        $parent = User::factory()->create(['role' => UserRole::PARENT]);
        $guardian = Guardian::factory()->create(['user_id' => $parent->id]);

        $this->assertEquals($guardian->id, $parent->guardian->id);
        $this->assertEquals($parent->id, $guardian->user->id);
    }

    /** @test */
    public function students_are_not_users()
    {
        $student = Student::factory()->create();

        // Students should not have user_id or any user relationship
        $this->assertNull($student->user_id ?? null);
        
        // Students are just records, not system users
        $this->assertInstanceOf(Student::class, $student);
        $this->assertNotInstanceOf(User::class, $student);
    }
}

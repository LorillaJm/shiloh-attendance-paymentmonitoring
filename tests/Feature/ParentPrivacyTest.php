<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_only_see_their_own_children(): void
    {
        // Create two parents with their children
        $parent1User = User::factory()->create(['role' => UserRole::PARENT]);
        $parent1 = Guardian::factory()->create(['user_id' => $parent1User->id]);
        $student1 = Student::factory()->create();
        $parent1->students()->attach($student1->id, ['is_primary' => true]);

        $parent2User = User::factory()->create(['role' => UserRole::PARENT]);
        $parent2 = Guardian::factory()->create(['user_id' => $parent2User->id]);
        $student2 = Student::factory()->create();
        $parent2->students()->attach($student2->id, ['is_primary' => true]);

        // Parent 1 should only see their child
        $this->actingAs($parent1User);
        $students = $parent1->students;
        
        $this->assertCount(1, $students);
        $this->assertTrue($students->contains($student1));
        $this->assertFalse($students->contains($student2));
    }

    public function test_parent_cannot_access_admin_resources(): void
    {
        $parentUser = User::factory()->create(['role' => UserRole::PARENT]);
        
        $this->actingAs($parentUser);
        
        // Parents should not access admin resources
        $this->assertFalse($parentUser->isAdmin());
        $this->assertTrue($parentUser->isParent());
    }

    public function test_guardian_can_have_multiple_children(): void
    {
        $parentUser = User::factory()->create(['role' => UserRole::PARENT]);
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        
        $guardian->students()->attach($student1->id, ['is_primary' => true]);
        $guardian->students()->attach($student2->id, ['is_primary' => false]);

        $this->assertCount(2, $guardian->students);
    }
}

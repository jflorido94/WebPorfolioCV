<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RemoveSortOrderMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sort_order_column_does_not_exist_in_experiences(): void
    {
        $this->assertFalse(Schema::hasColumn('experiences', 'sort_order'));
    }

    public function test_sort_order_column_does_not_exist_in_education(): void
    {
        $this->assertFalse(Schema::hasColumn('education', 'sort_order'));
    }

    public function test_sort_order_column_does_not_exist_in_skills(): void
    {
        $this->assertFalse(Schema::hasColumn('skills', 'sort_order'));
    }

    public function test_sort_order_column_does_not_exist_in_courses(): void
    {
        $this->assertFalse(Schema::hasColumn('courses', 'sort_order'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Training;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->travel(-1)->hours();
        $this->seed();
        $this->travelBack();

        $this->user = User::first();
    }

    public function test_can_view_trainings()
    {
        $this->actingAs($this->user)
            ->get('/trainings')
            ->assertStatus(200)
            ->assertPropCount('trainings.data', 10);
    }

    public function test_can_search_for_training_start_time()
    {
        $training_count = Training::whereYear('start_at', Carbon::now()->year)->count();

        $this->actingAs($this->user)
            ->get('/trainings?start_at=this_year')
            ->assertStatus(200)
            ->assertPropValue('trainings.total', $training_count);
    }

    public function test_can_search_for_training_text()
    {
        factory(Training::class)->create([
            'name' => 'training for validation',
            'place' => 'test place',
            'start_at' => Carbon::now(),
            'length' => 120,
            'max_attendees' => 10,
            'can_attend_more' => false,
        ]);

        $this->actingAs($this->user)
            ->get('/trainings?search=validation')
            ->assertStatus(200)
            ->assertPropCount('trainings.data', 1)
            ->assertPropValue('trainings.data', function ($training) {
                $this->assertEquals('training for validation', $training[0]['name']);
            });
    }

    public function test_user_can_attend_training()
    {
        $training = factory(Training::class)->create([
            'name' => 'training for validation',
            'place' => 'test place',
            'start_at' => Carbon::now()->addDay(),
            'length' => 120,
            'max_attendees' => 2,
            'can_attend_more' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/trainings/{$training->id}/attend", [])
            ->assertStatus(302)
            ->assertSessionHas('success', 'Sikeresen jelentkeztél az edzésre.');
    }

    public function test_cannot_attend_multiple_trainings_within_threshold()
    {
        $first = factory(Training::class)->create([
            'name' => 'training for validation',
            'place' => 'test place',
            'start_at' => Carbon::now()->addDay(),
            'length' => 120,
            'max_attendees' => 2,
            'can_attend_more' => false,
        ]);

        $second = factory(Training::class)->create([
            'name' => 'training for validation',
            'place' => 'test place',
            'start_at' => Carbon::now()->addDay(),
            'length' => 120,
            'max_attendees' => 2,
            'can_attend_more' => false,
        ]);

        $this->actingAs($this->user)
            ->post("/trainings/{$first->id}/attend", []);

        $this->actingAs($this->user)
            ->post("/trainings/{$second->id}/attend", [])
            ->assertStatus(302)
            ->assertSessionHas('error', 'Nemrég jelentkeztél már egy edzésre. Próbáld újra 10 perc múlva.');
    }

    public function test_cannot_attend_full_training()
    {
        $training = factory(Training::class)->create([
            'name' => 'training for validation',
            'place' => 'test place',
            'start_at' => Carbon::now()->addDay(),
            'length' => 120,
            'max_attendees' => 1,
            'can_attend_more' => false,
        ]);

        $this->actingAs(User::where('id', '<>', $this->user->id)->first())
            ->post("/trainings/{$training->id}/attend", []);

        $this->actingAs($this->user)
            ->post("/trainings/{$training->id}/attend", [])
            ->assertStatus(302)
            ->assertSessionHas('error', 'Már megtelt az edzés, nem lehet rá jelentkezni.');
    }

    public function test_can_withdraw_from_training()
    {
        $training = factory(Training::class)->create([
            'name' => 'training for validation',
            'place' => 'test place',
            'start_at' => Carbon::now()->addDay(),
            'length' => 120,
            'max_attendees' => 2,
            'can_attend_more' => false,
        ]);

        $this->actingAs($this->user)
            ->post("/trainings/{$training->id}/attend", []);

        $this->actingAs($this->user)
            ->delete("/trainings/{$training->id}/withdraw", [])
            ->assertStatus(302)
            ->assertSessionHas('success', 'Sikeresen visszavontad a jelentkezésed.');
    }

}

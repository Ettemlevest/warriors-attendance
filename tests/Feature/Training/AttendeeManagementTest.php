<?php

use App\Filament\Resources\TrainingResource\Pages\EditTraining;
use App\Filament\Resources\TrainingResource\Pages\ViewTraining;
use App\Filament\Resources\TrainingResource\RelationManagers\AttendeesRelationManager;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

describe('viewing attendees', function () {
    it('admin can see the attendees relation manager on training view', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->assertSuccessful();
    });

    it('regular user can see the attendees list on training view', function () {
        $user = User::factory()->create();
        $training = Training::factory()->create();

        $this->actingAs($user);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->assertSuccessful();
    });

    it('admin sees attended, extra, and subscription columns', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertCanRenderTableColumn('attended')
            ->assertCanRenderTableColumn('extra')
            ->assertCanRenderTableColumn('subscription_exists');
    });

    it('regular user does not see attended, extra, and subscription columns', function () {
        $user = User::factory()->create();
        $training = Training::factory()->create();
        TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($user);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableColumnHidden('attended')
            ->assertTableColumnHidden('extra')
            ->assertTableColumnHidden('subscription_exists');
    });
});

describe('adding attendees', function () {
    it('admin can manually add an attendee to a training', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])
            ->callTableAction('create', data: ['user_id' => $user->id]);

        $this->assertDatabaseHas('trainings_attendance', [
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);
    });

    it('admin can search for users when adding an attendee', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->assertTableActionExists('create');
    });
});

describe('confirming attendance', function () {
    it('admin can confirm attendance for a single attendee', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $attendance = TrainingAttendance::factory()->for($training)->create(['attended' => false]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('confirm_attendance', $attendance);

        expect($attendance->fresh()->attended)->toBeTrue();
    });

    it('confirming attendance sets attended to true', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $attendance = TrainingAttendance::factory()->for($training)->create(['attended' => false]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('confirm_attendance', $attendance);

        expect($attendance->fresh()->attended)->toBeTrue();
    });

    it('confirming attendance auto-associates the user live subscription', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['sessions' => 10]);
        $subscription = Subscription::factory()->for($plan)->for($user)->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->for($user)
            ->create(['attended' => false, 'subscription_id' => null]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('confirm_attendance', $attendance);

        expect($attendance->fresh()->subscription_id)->toBe($subscription->id);
    });

    it('confirming attendance triggers subscription expiry check', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['sessions' => 1]);
        $subscription = Subscription::factory()->for($plan)->for($user)->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->for($user)
            ->create(['attended' => false, 'subscription_id' => null]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('confirm_attendance', $attendance);

        expect($subscription->fresh()->expired_at)->not->toBeNull();
    });

    it('confirming attendance when user has no subscription still sets attended', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $user = User::factory()->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->for($user)
            ->create(['attended' => false]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('confirm_attendance', $attendance);

        expect($attendance->fresh())
            ->attended->toBeTrue()
            ->subscription_id->toBeNull();
    });

    it('confirm button is hidden when already attended', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->attended()
            ->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->assertTableActionHidden('confirm_attendance', $attendance);
    });

    it('regular user cannot see the confirm button', function () {
        $user = User::factory()->create();
        $training = Training::factory()->past()->create();
        $attendance = TrainingAttendance::factory()->for($training)->create(['attended' => false]);

        $this->actingAs($user);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->assertTableActionHidden('confirm_attendance', $attendance);
    });
});

describe('rejecting attendance', function () {
    it('admin can reject attendance for a single attendee', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->attended()
            ->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('not_attended', $attendance);

        expect($attendance->fresh()->attended)->toBeFalse();
    });

    it('rejecting attendance sets attended to false', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->attended()
            ->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('not_attended', $attendance);

        expect($attendance->fresh()->attended)->toBeFalse();
    });

    it('rejecting attendance dissociates the subscription', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['sessions' => 10]);
        $subscription = Subscription::factory()->for($plan)->for($user)->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->for($user)
            ->attended()
            ->create(['subscription_id' => $subscription->id]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('not_attended', $attendance);

        expect($attendance->fresh()->subscription_id)->toBeNull();
    });

    it('rejecting attendance recalculates subscription expiry', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['sessions' => 1]);
        $subscription = Subscription::factory()->for($plan)->for($user)->create([
            'expired_at' => now(),
        ]);
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->for($user)
            ->attended()
            ->create(['subscription_id' => $subscription->id]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableAction('not_attended', $attendance);

        expect($subscription->fresh()->expired_at)->toBeNull();
    });

    it('reject button is hidden when not attended', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $attendance = TrainingAttendance::factory()->for($training)->create(['attended' => false]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->assertTableActionHidden('not_attended', $attendance);
    });

    it('regular user cannot see the reject button', function () {
        $user = User::factory()->create();
        $training = Training::factory()->create();
        $attendance = TrainingAttendance::factory()
            ->for($training)
            ->attended()
            ->create();

        $this->actingAs($user);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->assertTableActionHidden('not_attended', $attendance);
    });
});

describe('bulk attendance actions', function () {
    it('admin can bulk confirm attendance', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $attendances = TrainingAttendance::factory()
            ->for($training)
            ->count(3)
            ->create(['attended' => false]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableBulkAction('bulk_attendance_confirmation', $attendances);

        foreach ($attendances as $attendance) {
            expect($attendance->fresh()->attended)->toBeTrue();
        }
    });

    it('admin can bulk reject attendance', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $attendances = TrainingAttendance::factory()
            ->for($training)
            ->attended()
            ->count(3)
            ->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->callTableBulkAction('bulk_attendance_rejection', $attendances);

        foreach ($attendances as $attendance) {
            expect($attendance->fresh()->attended)->toBeFalse();
        }
    });

    it('regular user cannot see bulk attendance actions', function () {
        $user = User::factory()->create();
        $training = Training::factory()->past()->create();

        $this->actingAs($user);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableBulkActionHidden('bulk_attendance_confirmation')
            ->assertTableBulkActionHidden('bulk_attendance_rejection');
    });
});

describe('deleting attendees', function () {
    it('admin can delete an attendee from a training', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $attendance = TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->callTableAction('delete', $attendance);

        $this->assertDatabaseMissing('trainings_attendance', ['id' => $attendance->id]);
    });

    it('admin can bulk delete attendees', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $attendances = TrainingAttendance::factory()->for($training)->count(3)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->callTableBulkAction('delete', $attendances);

        foreach ($attendances as $attendance) {
            $this->assertDatabaseMissing('trainings_attendance', ['id' => $attendance->id]);
        }
    });
});

describe('filtering attendees', function () {
    it('admin can filter attendees by attended status', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $attended = TrainingAttendance::factory()->for($training)->attended()->create();
        $notAttended = TrainingAttendance::factory()->for($training)->create(['attended' => false]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->filterTable('attended')
            ->assertCanSeeTableRecords([$attended])
            ->assertCanNotSeeTableRecords([$notAttended]);
    });

    it('admin can filter attendees by extra status', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $extra = TrainingAttendance::factory()->for($training)->create(['extra' => true]);
        $normal = TrainingAttendance::factory()->for($training)->create(['extra' => false]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->filterTable('extra')
            ->assertCanSeeTableRecords([$extra])
            ->assertCanNotSeeTableRecords([$normal]);
    });

    it('admin can filter attendees by subscription usage', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->create();
        $withSub = TrainingAttendance::factory()->for($training)->create([
            'subscription_id' => $subscription->id,
        ]);
        $withoutSub = TrainingAttendance::factory()->for($training)->create([
            'subscription_id' => null,
        ]);

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->filterTable('subscription_id', true)
            ->assertCanSeeTableRecords([$withSub])
            ->assertCanNotSeeTableRecords([$withoutSub]);
    });

    it('regular user does not see admin-only filters', function () {
        $user = User::factory()->create();
        $training = Training::factory()->create();

        $this->actingAs($user);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableFilterHidden('attended')
            ->assertTableFilterHidden('extra')
            ->assertTableFilterHidden('subscription_id');
    });
});

describe('bulk confirming a mix of confirmed and pending attendees', function () {
    it('succeeds and links the pending attendee to their live subscription', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create(['sessions' => 10]);
        $confirmedUser = User::factory()->create();
        $pendingUser = User::factory()->create();
        $confirmedSubscription = Subscription::factory()->for($plan)->for($confirmedUser)->create();
        $pendingSubscription = Subscription::factory()->for($plan)->for($pendingUser)->create();
        $training = Training::factory()->past()->create();
        $confirmed = TrainingAttendance::factory()->for($training)->for($confirmedUser)->create([
            'subscription_id' => $confirmedSubscription->id,
            'attended' => true,
        ]);
        $pending = TrainingAttendance::factory()->for($training)->for($pendingUser)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->callTableBulkAction('bulk_attendance_confirmation', [$confirmed->getKey(), $pending->getKey()]);

        expect($confirmed->fresh()->attended)->toBeTrue()
            ->and($confirmed->fresh()->subscription_id)->toBe($confirmedSubscription->id)
            ->and($pending->fresh()->attended)->toBeTrue()
            ->and($pending->fresh()->subscription_id)->toBe($pendingSubscription->id);
    });
});

describe('capacity when adding attendees', function () {
    it('admin added attendee is flagged extra when the training is at capacity', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->full()->create(['can_attend_more' => true]);
        $user = User::factory()->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->callTableAction('create', data: ['user_id' => $user->id]);

        $this->assertDatabaseHas('trainings_attendance', [
            'training_id' => $training->id,
            'user_id' => $user->id,
            'extra' => true,
        ]);
    });

    it('admin added attendee is not flagged extra below capacity', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create(['max_attendees' => 5]);
        $user = User::factory()->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->callTableAction('create', data: ['user_id' => $user->id]);

        $this->assertDatabaseHas('trainings_attendance', [
            'training_id' => $training->id,
            'user_id' => $user->id,
            'extra' => false,
        ]);
    });
});

describe('confirmation timing', function () {
    it('hides the confirm actions before the training has started', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create(['start_at' => now()->addDay()]);
        $attendance = TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableActionHidden('confirm_attendance', $attendance)
            ->assertTableBulkActionHidden('bulk_attendance_confirmation');
    });

    it('hides the confirm actions while the training is in progress', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create(['start_at' => now()->subMinutes(30), 'length' => 60]);
        $attendance = TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableActionHidden('confirm_attendance', $attendance)
            ->assertTableBulkActionHidden('bulk_attendance_confirmation');
    });

    it('shows the confirm actions once the training has ended', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->create(['start_at' => now()->subHours(2), 'length' => 60]);
        $attendance = TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableActionVisible('confirm_attendance', $attendance)
            ->assertTableBulkActionVisible('bulk_attendance_confirmation');
    });

    it('keeps the reject action available for confirmed rows', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $attendance = TrainingAttendance::factory()->for($training)->attended()->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])->assertTableActionVisible('not_attended', $attendance);
    });
});

describe('deleting confirmed attendees', function () {
    it('hides the delete action for a confirmed attendance', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $confirmed = TrainingAttendance::factory()->for($training)->attended()->create();
        $pending = TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])
            ->assertTableActionHidden('delete', $confirmed)
            ->assertTableActionVisible('delete', $pending);
    });

    it('refuses bulk delete when a confirmed attendance is selected', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $confirmed = TrainingAttendance::factory()->for($training)->attended()->create();
        $pending = TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])
            ->callTableBulkAction('delete', [$confirmed->getKey(), $pending->getKey()])
            ->assertNotified('Megerősített részvétel nem törölhető');

        $this->assertDatabaseHas('trainings_attendance', ['id' => $confirmed->id]);
        $this->assertDatabaseHas('trainings_attendance', ['id' => $pending->id]);
    });

    it('bulk deletes pending attendances when none of them is confirmed', function () {
        $admin = User::factory()->admin()->create();
        $training = Training::factory()->past()->create();
        $first = TrainingAttendance::factory()->for($training)->create();
        $second = TrainingAttendance::factory()->for($training)->create();

        $this->actingAs($admin);

        livewire(AttendeesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->callTableBulkAction('delete', [$first->getKey(), $second->getKey()]);

        $this->assertDatabaseMissing('trainings_attendance', ['id' => $first->id]);
        $this->assertDatabaseMissing('trainings_attendance', ['id' => $second->id]);
    });
});

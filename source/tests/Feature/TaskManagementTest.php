<?php
namespace Tests\Feature;
use App\Mail\TaskCompleted;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
class TaskManagementTest extends TestCase {
    use RefreshDatabase;
    private function data(array $overrides = []): array {
        return array_merge(['title'=>'Submit assessment','description'=>'Check all features','due_date'=>'2026-11-20','priority'=>'high','status'=>'pending'], $overrides);
    }
    public function test_public_pages_are_accessible(): void {
        foreach (['/', '/tasks', '/profile', '/about'] as $url) { $this->get($url)->assertOk(); }
    }
    public function test_guests_cannot_manage_tasks(): void {
        $task = Task::create($this->data());
        $this->get('/tasks/create')->assertRedirect('/login');
        $this->get("/tasks/{$task->id}/edit")->assertRedirect('/login');
        $this->post('/tasks', $this->data())->assertRedirect('/login');
        $this->patch("/tasks/{$task->id}", $this->data())->assertRedirect('/login');
        $this->delete("/tasks/{$task->id}")->assertRedirect('/login');
    }
    public function test_authenticated_user_can_create_and_update(): void {
        $this->actingAs(User::factory()->create());
        $this->post('/tasks', $this->data())->assertRedirect(route('tasks.index'));
        $task = Task::firstOrFail();
        $this->patch(route('tasks.update', $task), $this->data(['title'=>'Updated task']))->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id'=>$task->id,'title'=>'Updated task']);
    }
    public function test_validation_rejects_invalid_fields(): void {
        $this->actingAs(User::factory()->create());
        $this->post('/tasks', $this->data(['title'=>'','due_date'=>'bad','priority'=>'urgent','status'=>'unknown']))
            ->assertSessionHasErrors(['title','due_date','priority','status']);
        $this->assertDatabaseCount('tasks', 0);
        $task = Task::create($this->data());
        $this->patch(route('tasks.update', $task), $this->data(['title'=>str_repeat('x',151)]))->assertSessionHasErrors('title');
        $this->assertSame('Submit assessment', $task->fresh()->title);
    }
    public function test_completion_sends_once_until_task_is_reopened(): void {
        Mail::fake(); config(['tasks.notification_recipient'=>'reviewer@example.test']);
        $this->actingAs(User::factory()->create()); $task = Task::create($this->data());
        $this->patch(route('tasks.update', $task), $this->data(['status'=>'in_progress']))->assertSessionHasNoErrors();
        Mail::assertNothingSent();
        $this->patch(route('tasks.update', $task), $this->data(['status'=>'done']))->assertSessionHasNoErrors();
        Mail::assertSent(TaskCompleted::class, fn ($mail) => $mail->hasTo('reviewer@example.test') && $mail->task->id === $task->id);
        Mail::assertSentCount(1);
        $this->patch(route('tasks.update', $task), $this->data(['title'=>'Still finished','status'=>'done']))->assertSessionHasNoErrors();
        Mail::assertSentCount(1);
        $this->patch(route('tasks.update', $task), $this->data(['status'=>'pending']));
        $this->patch(route('tasks.update', $task), $this->data(['status'=>'done']));
        Mail::assertSentCount(2);
    }
    public function test_archive_keeps_record_but_hides_it(): void {
        $this->actingAs(User::factory()->create()); $task = Task::create($this->data());
        $this->delete(route('tasks.destroy', $task))->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id'=>$task->id,'is_archived'=>true]);
        $this->get('/tasks')->assertDontSee('Submit assessment');
        $this->get(route('tasks.show', $task))->assertNotFound();
        $this->get(route('tasks.edit', $task))->assertNotFound();
        $this->patch(route('tasks.update', $task), $this->data())->assertNotFound();
    }
}

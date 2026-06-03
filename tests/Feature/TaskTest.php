<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;

test('index returns all tasks', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(3);
});

test('store creates a new task', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name' => 'Nueva tarea',
        'user_id' => $user->id,
    ]);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'Nueva tarea', 'user_id' => $user->id]);

    $this->assertDatabaseHas('tasks', ['name' => 'Nueva tarea', 'user_id' => $user->id]);
});

test('store fails when name is missing', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'user_id' => $user->id,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('store fails when user_id does not exist', function () {
    $response = $this->postJson('/api/tasks', [
        'name' => 'Nueva tarea',
        'user_id' => 999,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['user_id']);
});

test('show returns a single task', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment(['id' => $task->id, 'name' => $task->name]);
});

test('show returns 404 for non-existent task', function () {
    $response = $this->getJson('/api/tasks/999');

    $response->assertNotFound();
});

test('update modifies an existing task', function () {
    $task = Task::factory()->create();
    $newUser = User::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name' => 'Tarea actualizada',
        'user_id' => $newUser->id,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Tarea actualizada', 'user_id' => $newUser->id]);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Tarea actualizada']);
});

test('update fails when name is missing', function () {
    $task = Task::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'user_id' => $task->user_id,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('update returns 404 for non-existent task', function () {
    $user = User::factory()->create();

    $response = $this->putJson('/api/tasks/999', [
        'name' => 'Tarea actualizada',
        'user_id' => $user->id,
    ]);

    $response->assertNotFound();
});

test('destroy deletes a task', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('destroy returns 404 for non-existent task', function () {
    $response = $this->deleteJson('/api/tasks/999');

    $response->assertNotFound();
});

// --- Status ---

test('new task has pendiente status by default', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name'    => 'Tarea sin estado',
        'user_id' => $user->id,
    ]);

    $response->assertCreated()
        ->assertJsonFragment(['status' => TaskStatus::Pendiente->value]);
});

test('store accepts a custom status', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name'    => 'Tarea en proceso',
        'user_id' => $user->id,
        'status'  => TaskStatus::EnProceso->value,
    ]);

    $response->assertCreated()
        ->assertJsonFragment(['status' => TaskStatus::EnProceso->value]);
});

test('store rejects an invalid status', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name'    => 'Tarea inválida',
        'user_id' => $user->id,
        'status'  => 'invalido',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('updateStatus changes task to en_proceso', function () {
    $task = Task::factory()->create(['status' => TaskStatus::Pendiente]);

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => TaskStatus::EnProceso->value,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['status' => TaskStatus::EnProceso->value]);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => TaskStatus::EnProceso->value]);
});

test('updateStatus marks task as finalizado', function () {
    $task = Task::factory()->create(['status' => TaskStatus::EnProceso]);

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => TaskStatus::Finalizado->value,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['status' => TaskStatus::Finalizado->value]);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => TaskStatus::Finalizado->value]);
});

test('updateStatus marks task back to pendiente', function () {
    $task = Task::factory()->create(['status' => TaskStatus::Finalizado]);

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => TaskStatus::Pendiente->value,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['status' => TaskStatus::Pendiente->value]);
});

test('updateStatus rejects an invalid status value', function () {
    $task = Task::factory()->create();

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => 'terminado',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('updateStatus requires status field', function () {
    $task = Task::factory()->create();

    $response = $this->patchJson("/api/tasks/{$task->id}/status", []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('updateStatus returns 404 for non-existent task', function () {
    $response = $this->patchJson('/api/tasks/999/status', [
        'status' => TaskStatus::Finalizado->value,
    ]);

    $response->assertNotFound();
});

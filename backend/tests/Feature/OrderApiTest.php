<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_require_authentication(): void
    {
        $this->getJson('/api/orders')->assertUnauthorized();
        $this->getJson('/api/orders/1')->assertUnauthorized();
        $this->postJson('/api/orders', $this->validOrder())->assertUnauthorized();
        $this->putJson('/api/orders/1', $this->validOrder())->assertUnauthorized();
        $this->deleteJson('/api/orders/1')->assertUnauthorized();
    }

    public function test_owner_can_create_read_update_and_delete_an_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($owner);

        $input = $this->validOrder();
        unset($input['status']);
        $input['user_id'] = $other->id;
        $input['total_price'] = 1;

        $created = $this->postJson('/api/orders', $input)
            ->assertCreated()
            ->assertJsonPath('user_id', $owner->id)
            ->assertJsonPath('total_price', 75_000)
            ->assertJsonPath('status', 'pending');

        $id = $created->json('id');

        $this->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $id);

        $this->getJson("/api/orders/{$id}")
            ->assertOk()
            ->assertJsonPath('id', $id);

        $updated = array_merge($this->validOrder(), [
            'quantity' => 4,
            'unit_price' => 30_000,
            'status' => 'processing',
            'user_id' => $other->id,
            'total_price' => 1,
        ]);

        $this->putJson("/api/orders/{$id}", $updated)
            ->assertOk()
            ->assertJsonPath('user_id', $owner->id)
            ->assertJsonPath('total_price', 120_000)
            ->assertJsonPath('status', 'processing');

        $this->deleteJson("/api/orders/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('orders', ['id' => $id]);
    }

    public function test_users_cannot_access_another_users_orders(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->for($owner)->create();

        Sanctum::actingAs($other);

        $this->getJson('/api/orders')->assertOk()->assertExactJson([]);
        $this->getJson("/api/orders/{$order->id}")->assertNotFound();
        $this->putJson("/api/orders/{$order->id}", $this->validOrder())
            ->assertNotFound();
        $this->deleteJson("/api/orders/{$order->id}")->assertNotFound();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_create_and_update_reject_invalid_order_data(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);

        $invalid = array_merge($this->validOrder(), [
            'customer_name' => '',
            'quantity' => 0,
            'unit_price' => 1.5,
            'status' => 'delivered',
            'due_date' => 'not-a-date',
        ]);

        $this->postJson('/api/orders', $invalid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'customer_name',
                'quantity',
                'unit_price',
                'status',
                'due_date',
            ]);

        $this->assertDatabaseCount('orders', 0);

        $order = Order::factory()->for($owner)->create([
            'quantity' => 2,
            'unit_price' => 25_000,
            'total_price' => 50_000,
        ]);

        $this->putJson("/api/orders/{$order->id}", $invalid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['quantity', 'unit_price', 'status']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_price' => 50_000,
        ]);
    }

    private function validOrder(): array
    {
        return [
            'customer_name' => 'Bu Sari',
            'product_name' => 'Plakat Akrilik',
            'quantity' => 3,
            'unit_price' => 25_000,
            'status' => 'pending',
            'due_date' => '2030-01-15',
            'notes' => 'Untuk wisuda',
        ];
    }
}

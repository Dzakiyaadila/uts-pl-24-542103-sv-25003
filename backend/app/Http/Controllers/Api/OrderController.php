<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->orders()->latest()->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request, creating: true);

        $order = $request->user()->orders()->make($data);
        $order->total_price = $data['quantity'] * $data['unit_price'];
        $order->save();

        return response()->json($order->fresh(), 201);
    }

    public function show(Request $request, string $order): JsonResponse
    {
        return response()->json(
            $request->user()->orders()->findOrFail($order)
        );
    }

    public function update(Request $request, string $order): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($order);
        $data = $this->validatedData($request, creating: false);

        $order->fill($data);
        $order->total_price = $data['quantity'] * $data['unit_price'];
        $order->save();

        return response()->json($order->fresh());
    }

    public function destroy(Request $request, string $order)
    {
        $request->user()->orders()->findOrFail($order)->delete();

        return response()->noContent();
    }

    private function validatedData(Request $request, bool $creating): array
    {
        return $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'unit_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'status' => [$creating ? 'sometimes' : 'required', 'in:pending,processing,completed,cancelled'],
            'due_date' => ['required', 'date_format:Y-m-d'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Enums\PurchaseOrderStatus;
use App\Features\PurchaseOrders\Application\ChangePurchaseOrderStatus;
use App\Features\PurchaseOrders\Application\CreatePurchaseOrder;
use App\Features\PurchaseOrders\Application\RegisterDelivery;
use App\Features\PurchaseOrders\Application\UpdatePurchaseOrder;
use App\Features\Quotations\Application\AddFollowUp;
use App\Features\Shared\Application\StoreAttachment;
use App\Http\Requests\ChangePurchaseOrderStatusRequest;
use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\StoreFollowUpRequest;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Models\Client;
use App\Models\PurchaseOrder;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PurchaseOrder::class);
        $filters = $request->only(['q', 'status', 'currency', 'client_id', 'user_id', 'date_from', 'date_to', 'follow_up', 'sort', 'direction']);

        return view('purchase-orders.index', [
            'orders' => PurchaseOrder::query()->with(['user', 'quotation'])->filtered($filters)->paginate(15)->withQueryString(),
            'filters' => $filters,
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => PurchaseOrderStatus::cases(),
            'currencies' => Currency::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PurchaseOrder::class);

        return view('purchase-orders.create', $this->formData());
    }

    public function store(StorePurchaseOrderRequest $request, CreatePurchaseOrder $create): RedirectResponse
    {
        $order = $create($request->user(), $request->validated());

        return redirect()->route('purchase-orders.show', $order)->with('success', 'Orden '.$order->folio.' creada.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('view', $purchaseOrder);
        $purchaseOrder->load([
            'items',
            'user',
            'client',
            'quotation',
            'converter',
            'deliveries.user',
            'deliveries.items',
            'followUps.user',
            'activities.user',
            'attachments.user',
        ]);

        return view('purchase-orders.show', [
            'order' => $purchaseOrder,
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('update', $purchaseOrder);
        $purchaseOrder->load('items');

        return view('purchase-orders.edit', ['order' => $purchaseOrder] + $this->formData());
    }

    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder, UpdatePurchaseOrder $update): RedirectResponse
    {
        $update($purchaseOrder, $request->user(), $request->validated());

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Orden actualizada.');
    }

    public function status(ChangePurchaseOrderStatusRequest $request, PurchaseOrder $purchaseOrder, ChangePurchaseOrderStatus $change): RedirectResponse
    {
        $change(
            $purchaseOrder,
            PurchaseOrderStatus::from($request->validated('status')),
            $request->user(),
            $request->validated('comment'),
        );

        return back()->with('success', 'Estado actualizado.');
    }

    public function delivery(StoreDeliveryRequest $request, PurchaseOrder $purchaseOrder, RegisterDelivery $register): RedirectResponse
    {
        $this->authorize('deliver', $purchaseOrder);
        $register($purchaseOrder, $request->user(), $request->validated());

        return back()->with('success', 'Entrega registrada.');
    }

    public function followUp(StoreFollowUpRequest $request, PurchaseOrder $purchaseOrder, AddFollowUp $addFollowUp): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);
        $addFollowUp($purchaseOrder, $request->user(), $request->validated());

        return back()->with('success', 'Seguimiento registrado.');
    }

    public function attachment(StoreAttachmentRequest $request, PurchaseOrder $purchaseOrder, StoreAttachment $store): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);
        $store($purchaseOrder, $request->file('file'), $request->user());

        return back()->with('success', 'Documento adjunto guardado.');
    }

    public function pdf(Request $request, PurchaseOrder $purchaseOrder): Response
    {
        $this->authorize('view', $purchaseOrder);
        $purchaseOrder->load(['items', 'user', 'quotation']);
        $pdf = Pdf::loadView('purchase-orders.pdf', ['order' => $purchaseOrder])->setPaper('letter');

        if ($request->boolean('download')) {
            return $pdf->download($purchaseOrder->folio.'.pdf');
        }

        return $pdf->stream($purchaseOrder->folio.'.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'clients' => Client::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'currencies' => Currency::cases(),
        ];
    }
}

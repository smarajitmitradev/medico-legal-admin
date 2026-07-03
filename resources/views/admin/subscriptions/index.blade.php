@extends('admin.layout.master')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
  .font-display { font-family: 'Lexend', sans-serif; }
  .font-mono { font-family: 'IBM Plex Mono', monospace; }

  .stat-card { transition: transform .15s ease, box-shadow .15s ease; }
  .stat-card:hover { transform: translateY(-2px); box-shadow: 0 12px 28px -8px rgba(15,23,42,0.12); }

  .row-fade { animation: rowIn .25s ease both; }
  @keyframes rowIn { from { opacity:0; transform: translateY(4px); } to { opacity:1; transform: translateY(0); } }

  .plan-rail { width: 4px; border-radius: 4px 0 0 4px; }

  /* plan tier accent colors */
  .rail-1month   { background: #94a3b8; }
  .rail-3month   { background: #c9a84c; }
  .rail-1year    { background: #3d6b4f; }
  .rail-7day     { background: #dc2626; }
  .rail-default  { background: #94a3b8; }

  .skel { background: linear-gradient(90deg,#eef2ff 25%,#f8fafc 37%,#eef2ff 63%); background-size: 400% 100%; animation: shimmer 1.4s infinite; }
  @keyframes shimmer { 0%{background-position:100% 50%} 100%{background-position:0% 50%} }
</style>

<div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-8">

  {{-- ══════════ HEADER ══════════ --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
      <h1 class="font-display text-2xl font-bold text-slate-900 flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
          <i class="fa-solid fa-receipt"></i>
        </span>
        Subscriptions
      </h1>
      <p class="text-sm text-slate-500 mt-1">Track plan activations, renewals, and billing status across all users.</p>
    </div>

    <div class="flex items-center gap-2">
      <button onclick="window.location.reload()" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 transition">
        <i class="fa-solid fa-rotate-right text-xs"></i> Refresh
      </button>
      <a href="{{ route('subscriptions.export', request()->query()) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-br from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 rounded-xl px-4 py-2.5 shadow-sm shadow-indigo-200 transition">
        <i class="fa-solid fa-download text-xs"></i> Export CSV
      </a>
    </div>
  </div>

  {{-- ══════════ STAT CARDS ══════════ --}}
  <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8">

    <div class="stat-card bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total</span>
        <i class="fa-solid fa-layer-group text-slate-300"></i>
      </div>
      <div class="font-display text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</div>
    </div>

    <div class="stat-card bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Active</span>
        <i class="fa-solid fa-circle-check text-emerald-300"></i>
      </div>
      <div class="font-display text-2xl font-bold text-emerald-600">{{ number_format($stats['active']) }}</div>
    </div>

    <div class="stat-card bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Expired</span>
        <i class="fa-solid fa-hourglass-end text-amber-300"></i>
      </div>
      <div class="font-display text-2xl font-bold text-amber-600">{{ number_format($stats['expired']) }}</div>
    </div>

    <div class="stat-card bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Cancelled</span>
        <i class="fa-solid fa-ban text-rose-300"></i>
      </div>
      <div class="font-display text-2xl font-bold text-rose-600">{{ number_format($stats['cancelled']) }}</div>
    </div>

    <div class="stat-card bg-white rounded-2xl border border-slate-100 p-5 shadow-sm col-span-2 sm:col-span-1">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Revenue</span>
        <i class="fa-solid fa-indian-rupee-sign text-indigo-300"></i>
      </div>
      <div class="font-display text-2xl font-bold text-indigo-600">₹{{ number_format($stats['revenue']) }}</div>
    </div>

  </div>

  {{-- ══════════ FILTER BAR ══════════ --}}
  <form method="GET" action="{{ route('subscriptions.index') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6">
    <div class="flex flex-col lg:flex-row gap-3">

      <div class="relative flex-1">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, email, or mobile..."
               class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 transition">
      </div>

      <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 transition">
        <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Statuses</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
      </select>

      <select name="plan" onchange="this.form.submit()" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 transition">
        <option value="all" {{ request('plan', 'all') == 'all' ? 'selected' : '' }}>All Plans</option>
        @foreach($plans as $p)
          <option value="{{ $p }}" {{ request('plan') == $p ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $p)) }}</option>
        @endforeach
      </select>

      <button type="submit" class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl px-5 py-2.5 transition">
        <i class="fa-solid fa-filter text-xs"></i> Filter
      </button>

      @if(request('search') || (request('status') && request('status') !== 'all') || (request('plan') && request('plan') !== 'all'))
        <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-700 px-3 py-2.5 transition">
          <i class="fa-solid fa-xmark text-xs"></i> Clear
        </a>
      @endif

    </div>
  </form>

  {{-- ══════════ TABLE ══════════ --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-100">
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">User</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Plan</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Plan ID</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Amount</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Started</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Expires</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Status</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Subscription ID</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Payment ID</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Signature</th>
            <th class="text-center font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Action</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-50">

        @forelse($subscriptions as $i => $sub)
          @php
            $isExpired = $sub->status === 'paid' && \Carbon\Carbon::parse($sub->expiry_date)->isPast();
            $isActive  = $sub->status === 'paid' && !$isExpired;
            $daysLeft  = $isActive ? now()->diffInDays(\Carbon\Carbon::parse($sub->expiry_date), false) : null;
            $expiringSoon = $isActive && $daysLeft !== null && $daysLeft <= 5;

            if (strpos($sub->plan_name, '7_day') !== false) {
                $railClass = 'rail-7day';
            } elseif (strpos($sub->plan_name, '1_month') !== false) {
                $railClass = 'rail-1month';
            } elseif (strpos($sub->plan_name, '3_month') !== false) {
                $railClass = 'rail-3month';
            } elseif (strpos($sub->plan_name, 'year') !== false) {
                $railClass = 'rail-1year';
            } else {
                $railClass = 'rail-default';
            }
          @endphp

          <tr class="row-fade hover:bg-slate-50/60 transition relative" style="animation-delay: {{ min($i * 30, 300) }}ms">

            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <span class="plan-rail {{ $railClass }} self-stretch"></span>
                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                  {{ strtoupper(substr($sub->user->full_name ?? $sub->user->first_name ?? 'U', 0, 1)) }}
                </div>
                <div>
                  <div class="font-semibold text-slate-800">
                    {{ $sub->user->full_name ?? trim(($sub->user->first_name ?? '').' '.($sub->user->last_name ?? '')) ?: 'Unknown User' }}
                  </div>
                  <div class="text-xs text-slate-400">{{ $sub->user->email ?? $sub->user->mobile_number ?? '—' }}</div>
                </div>
              </div>
            </td>

            <td class="px-6 py-4">
              <span class="font-medium text-slate-700">{{ ucwords(str_replace('_', ' ', $sub->plan_name)) }}</span>
              @if(strpos($sub->plan_name, '7_day') !== false)
                <span class="ml-1.5 text-[10px] font-bold text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded">TEST</span>
              @endif
            </td>

            <td class="px-6 py-4">
              <span class="font-mono text-xs text-slate-500 bg-slate-50 border border-slate-100 px-2 py-1 rounded-md">
                {{ $sub->plan_name }}
              </span>
            </td>

            <td class="px-6 py-4 font-mono text-slate-700">₹{{ number_format($sub->amount) }}</td>

            <td class="px-6 py-4 text-slate-500 font-mono text-xs">
              {{ \Carbon\Carbon::parse($sub->start_date)->format('d M Y') }}
            </td>

            <td class="px-6 py-4 font-mono text-xs">
              <div class="{{ $isExpired ? 'text-rose-500' : 'text-slate-500' }}">
                {{ \Carbon\Carbon::parse($sub->expiry_date)->format('d M Y') }}
              </div>
              @if($expiringSoon)
                <div class="text-[11px] text-amber-600 font-semibold mt-0.5">{{ $daysLeft }}d left</div>
              @endif
            </td>

            <td class="px-6 py-4">
              @if($sub->status === 'cancelled')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled
                </span>
              @elseif($isExpired)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Expired
                </span>
              @elseif($expiringSoon)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-700 bg-orange-50 border border-orange-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span> Expiring Soon
                </span>
              @else
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                </span>
              @endif
            </td>

            <td class="px-6 py-4">
              <span class="font-mono text-[11px] text-slate-400" title="{{ $sub->razorpay_subscription_id }}">
                {{ $sub->razorpay_subscription_id ? Str::limit($sub->razorpay_subscription_id, 18) : '—' }}
              </span>
            </td>

            <td class="px-6 py-4">
              <span class="font-mono text-[11px] text-slate-400" title="{{ $sub->razorpay_payment_id }}">
                {{ $sub->razorpay_payment_id ? Str::limit($sub->razorpay_payment_id, 18) : '—' }}
              </span>
            </td>

            <td class="px-6 py-4">
              @if($sub->razorpay_signature)
                <button type="button"
                        class="copy-sig-btn font-mono text-[11px] text-slate-400 hover:text-indigo-600 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 px-2 py-1 rounded-md transition"
                        data-sig="{{ $sub->razorpay_signature }}"
                        title="Click to copy full signature">
                  {{ Str::limit($sub->razorpay_signature, 14) }} <i class="fa-regular fa-copy ml-1"></i>
                </button>
              @else
                <span class="font-mono text-[11px] text-slate-400">—</span>
              @endif
            </td>

            <td class="px-6 py-4 text-center">
              <div class="inline-flex items-center gap-1.5">
                <button class="view-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                        data-id="{{ $sub->id }}" title="View details">
                  <i class="fa-solid fa-eye text-xs"></i>
                </button>

                @if($sub->status === 'paid' && !$isExpired)
                  <button class="cancel-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition"
                          data-id="{{ $sub->id }}" title="Cancel subscription">
                    <i class="fa-solid fa-ban text-xs"></i>
                  </button>
                @endif

                <button class="delete-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                        data-id="{{ $sub->id }}" title="Delete record">
                  <i class="fa-solid fa-trash text-xs"></i>
                </button>
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="11" class="px-6 py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 text-xl">
                  <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="text-slate-500 font-medium">No subscriptions found</div>
                <div class="text-slate-400 text-xs">Try adjusting your filters or search term.</div>
              </div>
            </td>
          </tr>
        @endforelse

        </tbody>
      </table>
    </div>

    {{-- ══════════ PAGINATION ══════════ --}}
    @if($subscriptions->hasPages())
      <div class="px-6 py-4 border-t border-slate-100">
        {{ $subscriptions->links() }}
      </div>
    @endif

  </div>

</div>

{{-- ══════════════════════════════════════════
   SUBSCRIPTION DETAIL MODAL
══════════════════════════════════════════ --}}
<div id="subModal" class="fixed inset-0 z-50 hidden">

  {{-- Backdrop --}}
  <div id="subModalBackdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>

  {{-- Panel wrapper --}}
  <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
    <div id="subModalPanel" class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl opacity-0 translate-y-3 scale-[0.98] transition-all duration-200 my-8">

      {{-- Close button --}}
      <button id="subModalClose" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition z-10">
        <i class="fa-solid fa-xmark"></i>
      </button>

      {{-- Loading state --}}
      <div id="subModalLoading" class="p-10">
        <div class="flex items-center gap-4 mb-6">
          <div class="skel w-14 h-14 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="skel h-4 w-1/3 rounded"></div>
            <div class="skel h-3 w-1/2 rounded"></div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="skel h-20 rounded-xl"></div>
          <div class="skel h-20 rounded-xl"></div>
          <div class="skel h-20 rounded-xl"></div>
          <div class="skel h-20 rounded-xl"></div>
        </div>
      </div>

      {{-- Content (filled by JS) --}}
      <div id="subModalContent" class="hidden"></div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function () {

    // ── Subscription detail modal ──
    const railColors = {
        '7_day':   { bg: 'bg-rose-50',    text: 'text-rose-600',    ring: 'ring-rose-100' },
        '1_month': { bg: 'bg-slate-100',  text: 'text-slate-600',   ring: 'ring-slate-200' },
        '3_month': { bg: 'bg-amber-50',   text: 'text-amber-600',   ring: 'ring-amber-100' },
        '1_year':  { bg: 'bg-emerald-50', text: 'text-emerald-600', ring: 'ring-emerald-100' },
        'default': { bg: 'bg-slate-100',  text: 'text-slate-600',   ring: 'ring-slate-200' },
    };

    function planColor(planName) {
        for (const key in railColors) {
            if (planName && planName.indexOf(key) !== -1) return railColors[key];
        }
        return railColors.default;
    }

    function statusBadge(status) {
        if (status === 'cancelled') {
            return '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-100 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled</span>';
        }
        if (status === 'expired') {
            return '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Expired</span>';
        }
        return '<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>';
    }

    function historyStatusDot(status) {
        if (status === 'cancelled') return '<span class="w-2 h-2 rounded-full bg-rose-400"></span>';
        if (status === 'paid') return '<span class="w-2 h-2 rounded-full bg-emerald-400"></span>';
        return '<span class="w-2 h-2 rounded-full bg-slate-300"></span>';
    }

    function openModal() {
        $('#subModal').removeClass('hidden');
        requestAnimationFrame(function () {
            $('#subModalBackdrop').removeClass('opacity-0');
            $('#subModalPanel').removeClass('opacity-0 translate-y-3 scale-[0.98]');
        });
        $('body').css('overflow', 'hidden');
    }

    function closeModal() {
        $('#subModalBackdrop').addClass('opacity-0');
        $('#subModalPanel').addClass('opacity-0 translate-y-3 scale-[0.98]');
        $('body').css('overflow', '');
        setTimeout(function () {
            $('#subModal').addClass('hidden');
            $('#subModalContent').addClass('hidden').html('');
            $('#subModalLoading').removeClass('hidden');
        }, 200);
    }

    $('#subModalClose').click(closeModal);
    $('#subModalBackdrop').click(closeModal);
    $(document).keydown(function (e) {
        if (e.key === 'Escape' && !$('#subModal').hasClass('hidden')) closeModal();
    });

    $('.view-btn').click(function () {
        let id = $(this).data('id');

        openModal();
        $('#subModalLoading').removeClass('hidden');
        $('#subModalContent').addClass('hidden');

        $.ajax({
            url: "{{ route('subscriptions.show', ':id') }}".replace(':id', id),
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res.success) {
                    toastr.error('Could not load subscription details');
                    closeModal();
                    return;
                }

                const sub = res.subscription;
                const user = res.user;
                const history = res.history;
                const color = planColor(sub.plan_name);

                let historyRows = '';
                history.forEach(function (h) {
                    historyRows += `
                        <div class="flex items-center justify-between gap-3 py-3 ${h.is_current ? 'bg-indigo-50/50 -mx-3 px-3 rounded-xl' : ''}">
                          <div class="flex items-center gap-3">
                            ${historyStatusDot(h.status)}
                            <div>
                              <div class="text-sm font-medium text-slate-700">${h.plan_label} ${h.is_current ? '<span class="text-[10px] font-bold text-indigo-500 bg-indigo-100 px-1.5 py-0.5 rounded ml-1">CURRENT</span>' : ''}</div>
                              <div class="text-xs text-slate-400 font-mono">${h.start_date} → ${h.expiry_date}</div>
                            </div>
                          </div>
                          <div class="text-sm font-mono font-semibold text-slate-600">₹${Number(h.amount).toLocaleString()}</div>
                        </div>`;
                });

                const initials = (user.name || 'U').trim().charAt(0).toUpperCase();

                const html = `
                  <div class="p-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                      <div class="w-14 h-14 rounded-full ${color.bg} ${color.text} flex items-center justify-center text-lg font-bold ring-4 ${color.ring} flex-shrink-0">
                        ${initials}
                      </div>
                      <div class="flex-1 min-w-0">
                        <h3 class="font-display text-lg font-bold text-slate-900 truncate">${user.name}</h3>
                        <div class="text-sm text-slate-400 truncate">${user.email || user.mobile || 'No contact info'}</div>
                      </div>
                      ${statusBadge(sub.status)}
                    </div>
                  </div>

                  <div class="p-8 pt-6">

                    <div class="grid grid-cols-2 gap-4 mb-6">
                      <div class="bg-slate-50 rounded-xl p-4">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Plan</div>
                        <div class="font-display font-bold text-slate-800">${sub.plan_label}</div>
                      </div>
                      <div class="bg-slate-50 rounded-xl p-4">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Amount</div>
                        <div class="font-display font-bold text-slate-800">₹${Number(sub.amount).toLocaleString()}</div>
                      </div>
                      <div class="bg-slate-50 rounded-xl p-4">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Started</div>
                        <div class="font-mono text-sm text-slate-700">${sub.start_date}</div>
                      </div>
                      <div class="bg-slate-50 rounded-xl p-4">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Expires</div>
                        <div class="font-mono text-sm text-slate-700">${sub.expiry_date}</div>
                        ${sub.status === 'active' && sub.days_left <= 5 ? `<div class="text-[11px] text-amber-600 font-semibold mt-1">${sub.days_left}d left</div>` : ''}
                      </div>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4 mb-6">
                      <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Razorpay Details</div>
                      <div class="space-y-2">
                        <div class="flex items-center justify-between gap-3">
                          <span class="text-xs text-slate-400">Subscription ID</span>
                          <span class="font-mono text-xs text-slate-600 truncate max-w-[220px]" title="${sub.razorpay_subscription_id || ''}">${sub.razorpay_subscription_id || '—'}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                          <span class="text-xs text-slate-400">Payment ID</span>
                          <span class="font-mono text-xs text-slate-600 truncate max-w-[220px]" title="${sub.razorpay_payment_id || ''}">${sub.razorpay_payment_id || '—'}</span>
                        </div>
                      </div>
                    </div>

                    <div>
                      <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-300"></i> Subscription History
                      </div>
                      <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto pr-1">
                        ${historyRows || '<div class="text-sm text-slate-400 py-4">No history found.</div>'}
                      </div>
                    </div>

                  </div>`;

                $('#subModalContent').html(html);
                $('#subModalLoading').addClass('hidden');
                $('#subModalContent').removeClass('hidden');
            },
            error: function () {
                toastr.error('Could not load subscription details');
                closeModal();
            }
        });
    });

    // Copy full razorpay signature to clipboard
    $('.copy-sig-btn').click(function () {
        let sig = $(this).data('sig');
        let $btn = $(this);

        navigator.clipboard.writeText(sig).then(function () {
            toastr.success('Signature copied to clipboard');
            let original = $btn.html();
            $btn.html('Copied! <i class="fa-solid fa-check ml-1"></i>');
            setTimeout(function () { $btn.html(original); }, 1200);
        }).catch(function () {
            toastr.error('Could not copy signature');
        });
    });

    // Delete subscription record
    $('.delete-btn').click(function () {
        let id = $(this).data('id');
        let $btn = $(this);

        if (confirm('Delete this subscription record? This cannot be undone.')) {
            $.ajax({
                url: "{{ route('subscriptions.destroy', ':id') }}".replace(':id', id),
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    toastr.success('Subscription record deleted');
                    $btn.closest('tr').fadeOut(250, function () { $(this).remove(); });
                },
                error: function () {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });

    // Cancel subscription — stops autopay on Razorpay immediately, then marks cancelled locally
    $('.cancel-btn').click(function () {
        let id = $(this).data('id');
        let $btn = $(this);

        if (confirm('Cancel this subscription? This will immediately stop autopay on Razorpay and cannot be undone.')) {
            $btn.prop('disabled', true).css('opacity', 0.5);

            $.ajax({
                url: "{{ route('subscriptions.cancel', ':id') }}".replace(':id', id),
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.success) {
                        toastr.success(res.message || 'Subscription cancelled on Razorpay');
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Could not cancel subscription');
                        $btn.prop('disabled', false).css('opacity', 1);
                    }
                },
                error: function (xhr) {
                    let msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Something went wrong while cancelling on Razorpay.';
                    toastr.error(msg);
                    $btn.prop('disabled', false).css('opacity', 1);
                }
            });
        }
    });

});
</script>

@endsection
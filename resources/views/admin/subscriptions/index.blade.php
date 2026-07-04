@extends('admin.layout.master')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
  .font-display {
    font-family: 'Lexend', sans-serif;
  }

  .font-mono {
    font-family: 'IBM Plex Mono', monospace;
  }

  .stat-card {
    transition: transform .15s ease, box-shadow .15s ease;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px -8px rgba(15, 23, 42, 0.12);
  }

  .row-fade {
    animation: rowIn .25s ease both;
  }

  @keyframes rowIn {
    from {
      opacity: 0;
      transform: translateY(4px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .plan-rail {
    width: 4px;
    border-radius: 4px 0 0 4px;
    flex-shrink: 0;
  }

  .rail-7day {
    background: #dc2626;
  }

  .rail-1month {
    background: #94a3b8;
  }

  .rail-6month {
    background: #c9a84c;
  }

  .rail-1year {
    background: #3d6b4f;
  }

  .rail-default {
    background: #94a3b8;
  }

  .skel {
    background: linear-gradient(90deg, #eef2ff 25%, #f8fafc 37%, #eef2ff 63%);
    background-size: 400% 100%;
    animation: shimmer 1.4s infinite;
  }

  @keyframes shimmer {
    0% {
      background-position: 100% 50%
    }

    100% {
      background-position: 0% 50%
    }
  }

  /* action tooltip */
  .action-btn {
    position: relative;
  }

  .action-btn:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 110%;
    left: 50%;
    transform: translateX(-50%);
    background: #1e293b;
    color: #fff;
    font-size: 10px;
    white-space: nowrap;
    padding: 3px 7px;
    border-radius: 5px;
    pointer-events: none;
    z-index: 10;
  }
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
      <p class="text-sm text-slate-500 mt-1">Manage plans, renewals, and billing status across all users.</p>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="window.location.reload()" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 transition">
        <i class="fa-solid fa-rotate-right text-xs"></i> Refresh
      </button>
      <a href="{{ route('subscriptions.export', request()->query()) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-br from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 rounded-xl px-4 py-2.5 shadow-sm shadow-indigo-200 transition">
        <i class="fa-solid fa-download text-xs"></i> Export CSV
      </a>
      {{-- Always visible for manual recovery when row doesn't exist --}}
      <button onclick="openRecoverModal('')" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-br from-amber-500 to-amber-400 hover:from-amber-600 hover:to-amber-500 rounded-xl px-4 py-2.5 shadow-sm transition">
        <i class="fa-solid fa-rotate text-xs"></i> Recover Payment
      </button>
      <button onclick="openDirectRefundModal()" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-br from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 rounded-xl px-4 py-2.5 shadow-sm transition">
        <i class="fa-solid fa-rotate-left text-xs"></i> Direct Refund
      </button>
    </div>
  </div>

  {{-- ══════════ STAT CARDS ══════════ --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-7 gap-4 mb-8">

    <div class="stat-card group relative bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden min-w-0">
      <span class="absolute inset-x-0 top-0 h-[3px] bg-slate-300"></span>
      <div class="p-4 lg:p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-sm">
            <i class="fa-solid fa-layer-group"></i>
          </span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</span>
        </div>
        <div class="font-display text-2xl lg:text-[26px] leading-none font-bold text-slate-900 truncate text-center">{{ number_format($stats['total']) }}</div>
      </div>
    </div>

    <div class="stat-card group relative bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden min-w-0">
      <span class="absolute inset-x-0 top-0 h-[3px] bg-emerald-500"></span>
      <div class="p-4 lg:p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
            <i class="fa-solid fa-circle-check"></i>
          </span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active</span>
        </div>
        <div class="font-display text-2xl lg:text-[26px] leading-none font-bold text-emerald-600 truncate text-center">{{ number_format($stats['active']) }}</div>
      </div>
    </div>

    <div class="stat-card group relative bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden min-w-0">
      <span class="absolute inset-x-0 top-0 h-[3px] bg-amber-500"></span>
      <div class="p-4 lg:p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
            <i class="fa-solid fa-hourglass-end"></i>
          </span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Expired</span>
        </div>
        <div class="font-display text-2xl lg:text-[26px] leading-none font-bold text-amber-600 truncate text-center">{{ number_format($stats['expired']) }}</div>
      </div>
    </div>

    <div class="stat-card group relative bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden min-w-0">
      <span class="absolute inset-x-0 top-0 h-[3px] bg-rose-500"></span>
      <div class="p-4 lg:p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
            <i class="fa-solid fa-ban"></i>
          </span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cancelled</span>
        </div>
        <div class="font-display text-2xl lg:text-[26px] leading-none font-bold text-rose-600 truncate text-center">{{ number_format($stats['cancelled']) }}</div>
      </div>
    </div>

    <div class="stat-card group relative bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden min-w-0">
      <span class="absolute inset-x-0 top-0 h-[3px] bg-red-500"></span>
      <div class="p-4 lg:p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm">
            <i class="fa-solid fa-circle-exclamation"></i>
          </span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Halted</span>
        </div>
        <div class="font-display text-2xl lg:text-[26px] leading-none font-bold text-red-600 truncate text-center">{{ number_format($stats['halted']) }}</div>
      </div>
    </div>

    <div class="stat-card group relative bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden min-w-0">
      <span class="absolute inset-x-0 top-0 h-[3px] bg-purple-500"></span>
      <div class="p-4 lg:p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
            <i class="fa-solid fa-rotate-left"></i>
          </span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Refunded</span>
        </div>
        <div class="font-display text-2xl lg:text-[26px] leading-none font-bold text-purple-600 truncate text-center">{{ number_format($stats['refunded']) }}</div>
      </div>
    </div>

    <div class="stat-card group relative bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden min-w-0 col-span-2 sm:col-span-1">
      <span class="absolute inset-x-0 top-0 h-[3px] bg-indigo-500"></span>
      <div class="p-4 lg:p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
            <i class="fa-solid fa-indian-rupee-sign"></i>
          </span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Revenue</span>
        </div>
        <div class="font-display text-2xl lg:text-[26px] leading-none font-bold text-indigo-600 truncate text-center">₹{{ number_format($stats['revenue']) }}</div>
      </div>
    </div>

  </div>

  {{-- ══════════ FILTER BAR ══════════ --}}
  <form method="GET" action="{{ route('subscriptions.index') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6">
    <div class="flex flex-col lg:flex-row gap-3">

      <div class="relative flex-1">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or mobile..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 transition">
      </div>

      <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 transition">
        <option value="all" {{ request('status','all')=='all'            ? 'selected':'' }}>All Statuses</option>
        <option value="active" {{ request('status')=='active'               ? 'selected':'' }}>Active</option>
        <option value="expired" {{ request('status')=='expired'              ? 'selected':'' }}>Expired</option>
        <option value="cancelled" {{ request('status')=='cancelled'            ? 'selected':'' }}>Cancelled</option>
        <option value="halted" {{ request('status')=='halted'               ? 'selected':'' }}>Halted</option>
        <option value="paused" {{ request('status')=='paused'               ? 'selected':'' }}>Paused</option>
        <option value="payment_failed" {{ request('status')=='payment_failed'       ? 'selected':'' }}>Payment Failed</option>
        <option value="refund_initiated" {{ request('status')=='refund_initiated'     ? 'selected':'' }}>Refund Initiated</option>
        <option value="refunded" {{ request('status')=='refunded'             ? 'selected':'' }}>Refunded</option>
        <option value="completed" {{ request('status')=='completed'            ? 'selected':'' }}>Completed</option>
      </select>

      <select name="plan" onchange="this.form.submit()" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 transition">
        <option value="all" {{ request('plan','all')=='all' ? 'selected':'' }}>All Plans</option>
        @foreach($plans as $p)
        <option value="{{ $p }}" {{ request('plan')==$p ? 'selected':'' }}>
          {{ ucwords(str_replace('_',' ',$p)) }}
        </option>
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
          <tr class="border-b border-slate-100 bg-slate-50/60">
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">User</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Plan</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Amount</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Started</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Expires</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Status</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Signeture</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Subscription ID</th>
            <th class="text-left font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Payment ID</th>
            <th class="text-center font-semibold text-slate-400 text-xs uppercase tracking-wide px-6 py-4">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-50">
          @forelse($subscriptions as $i => $sub)
          @php
          $isExpired = $sub->status === 'paid' && \Carbon\Carbon::parse($sub->expiry_date)->isPast();
          $isActive = $sub->status === 'paid' && !$isExpired;
          $daysLeft = $isActive ? now()->diffInDays(\Carbon\Carbon::parse($sub->expiry_date), false) : null;
          $expiringSoon = $isActive && $daysLeft !== null && $daysLeft <= 5; if (strpos($sub->plan_name, '7_day') !== false) {
            $railClass = 'rail-7day';
            } elseif (strpos($sub->plan_name, '1_month') !== false) {
            $railClass = 'rail-1month';
            } elseif (strpos($sub->plan_name, '6_month') !== false) {
            $railClass = 'rail-6month';
            } elseif (strpos($sub->plan_name, 'year') !== false) {
            $railClass = 'rail-1year';
            } else {
            $railClass = 'rail-default';
            }
            @endphp

            <tr class="row-fade hover:bg-slate-50/60 transition" style="animation-delay:{{ min($i * 30, 300) }}ms">

              {{-- User --}}
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <span class="plan-rail {{ $railClass }} self-stretch min-h-[40px]"></span>
                  <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($sub->user->full_name ?? $sub->user->first_name ?? 'U', 0, 1)) }}
                  </div>
                  <div>
                    <div class="font-semibold text-slate-800 text-sm">
                      {{ $sub->user->full_name ?? trim(($sub->user->first_name ?? '').' '.($sub->user->last_name ?? '')) ?: 'Unknown User' }}
                    </div>
                    <div class="text-xs text-slate-400">{{ $sub->user->email ?? $sub->user->mobile_number ?? '—' }}</div>
                  </div>
                </div>
              </td>

              {{-- Plan --}}
              <td class="px-6 py-4">
                <span class="font-medium text-slate-700 text-sm">{{ ucwords(str_replace('_',' ',$sub->plan_name)) }}</span>
                @if(str_contains($sub->plan_name, '7_day'))
                <span class="ml-1.5 text-[10px] font-bold text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded">TEST</span>
                @endif
              </td>

              {{-- Amount --}}
              <td class="px-6 py-4 font-mono text-slate-700 text-sm">₹{{ number_format($sub->amount) }}</td>

              {{-- Started --}}
              <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                {{ \Carbon\Carbon::parse($sub->start_date)->format('d M Y') }}
              </td>

              {{-- Expires --}}
              <td class="px-6 py-4 font-mono text-xs">
                <div class="{{ $isExpired ? 'text-rose-500 font-semibold' : 'text-slate-500' }}">
                  {{ \Carbon\Carbon::parse($sub->expiry_date)->format('d M Y') }}
                </div>
                @if($expiringSoon)
                <div class="text-[11px] text-amber-600 font-semibold mt-0.5">{{ $daysLeft }}d left</div>
                @endif
              </td>

              {{-- Status --}}
              <td class="px-6 py-4">
                @if($sub->status === 'cancelled')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled
                </span>
                @elseif($sub->status === 'halted')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Halted
                </span>
                @elseif($sub->status === 'paused')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-yellow-700 bg-yellow-50 border border-yellow-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Paused
                </span>
                @elseif($sub->status === 'payment_failed')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-700 bg-orange-50 border border-orange-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Payment Failed
                </span>
                @elseif($sub->status === 'refund_initiated')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span> Refund Initiated
                </span>
                @elseif($sub->status === 'refunded')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Refunded
                </span>
                @elseif($sub->status === 'completed')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span> Completed
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

              {{-- Razorpay Signeture --}}
              <td class="px-6 py-4">
                <span class="font-mono text-[11px] text-slate-400" title="{{ $sub->razorpay_signature }}">
                  {{ $sub->razorpay_signature ? Str::limit($sub->razorpay_signature, 18) : '—' }}
                </span>
              </td>

              {{-- Subscription ID --}}
              <td class="px-6 py-4">
                <span class="font-mono text-[11px] text-slate-400" title="{{ $sub->razorpay_subscription_id }}">
                  {{ $sub->razorpay_subscription_id ? Str::limit($sub->razorpay_subscription_id, 18) : '—' }}
                </span>
              </td>

              {{-- Payment ID --}}
              <td class="px-6 py-4">
                <span class="font-mono text-[11px] text-slate-400" title="{{ $sub->razorpay_payment_id }}">
                  {{ $sub->razorpay_payment_id ? Str::limit($sub->razorpay_payment_id, 18) : '—' }}
                </span>
              </td>

              {{-- Actions --}}
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-1">

                  {{-- View --}}
                  <button class="action-btn view-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-indigo-500 transition" data-id="{{ $sub->id }}" title="View Details">
                    <i class="fa-solid fa-eye text-xs"></i>
                  </button>

                  {{-- Cancel (active only) --}}
                  @if($isActive)
                  <button class="action-btn cancel-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-rose-500 transition" data-id="{{ $sub->id }}" title="Cancel Subscription">
                    <i class="fa-solid fa-ban text-xs"></i>
                  </button>
                  @endif

                  {{-- Pause (active only) --}}
                  @if($isActive)
                  <button class="action-btn pause-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-amber-500 transition" data-id="{{ $sub->id }}" title="Pause Subscription">
                    <i class="fa-solid fa-pause text-xs"></i>
                  </button>
                  @endif

                  {{-- Resume (paused only) --}}
                  @if($sub->status === 'paused')
                  <button class="action-btn resume-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-emerald-500 transition" data-id="{{ $sub->id }}" title="Resume Subscription">
                    <i class="fa-solid fa-play text-xs"></i>
                  </button>
                  @endif

                  {{-- Refund (not already refunded/completed) --}}
                  @if(!in_array($sub->status, ['refunded','refund_initiated','completed']))
                  <button class="action-btn refund-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-purple-500 transition" data-id="{{ $sub->id }}" title="Refund Payment">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                  </button>
                  @endif

                  {{-- Complete (active only) --}}
                  @if($isActive)
                  <button class="action-btn complete-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-teal-500 transition" data-id="{{ $sub->id }}" title="Mark as Completed">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                  </button>
                  @endif

                  {{-- Delete --}}
                  <button class="action-btn delete-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-red-500 transition" data-id="{{ $sub->id }}" title="Delete Record">
                    <i class="fa-solid fa-trash text-xs"></i>
                  </button>

                  {{-- Recover (row exists but not activated) --}}
                  @if($sub->razorpay_payment_id && $sub->status !== 'paid')
                  <button class="action-btn recover-modal-btn w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-orange-500 transition" data-paymentid="{{ $sub->razorpay_payment_id }}" title="Recover Payment">
                    <i class="fa-solid fa-rotate text-xs"></i>
                  </button>
                  @endif

                </div>
              </td>

            </tr>
            @empty
            <tr>
              <td colspan="9" class="px-6 py-16 text-center">
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
  <div id="subModalBackdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
    <div id="subModalPanel" class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl opacity-0 translate-y-3 scale-[0.98] transition-all duration-200 my-8">

      <button id="subModalClose" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition z-10">
        <i class="fa-solid fa-xmark"></i>
      </button>

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

      <div id="subModalContent" class="hidden"></div>
    </div>
  </div>
</div>


{{-- ══════════ RECOVERY MODAL ══════════ --}}
<div id="recoverModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('recoverModal').classList.add('hidden')"></div>

  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">

      <button onclick="document.getElementById('recoverModal').classList.add('hidden')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 flex items-center justify-center transition">
        <i class="fa-solid fa-xmark"></i>
      </button>

      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
          <i class="fa-solid fa-rotate"></i>
        </div>
        <div>
          <h3 class="font-display text-lg font-bold text-slate-900">Recover Payment</h3>
          <p class="text-xs text-slate-400">Force activate subscription from Razorpay Payment ID</p>
        </div>
      </div>

      <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
          <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 flex-shrink-0"></i>
          <div class="text-xs text-amber-700 leading-relaxed">
            Use only when payment was debited but subscription was <strong>not activated</strong> due to a server error.
            Enter the <strong>Razorpay Payment ID</strong> (starts with <code class="bg-amber-100 px-1 rounded">pay_</code>) from the Razorpay dashboard.
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
          Razorpay Payment ID
        </label>
        <input type="text" id="recoverPaymentId" placeholder="pay_XXXXXXXXXXXXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 font-mono focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-300 transition">
      </div>

      <div id="recoverResult" class="hidden mb-4 p-3 rounded-xl text-sm font-medium"></div>

      <button id="recoverBtn" onclick="doRecover()" class="w-full py-3 rounded-xl bg-gradient-to-br from-amber-500 to-amber-400 hover:from-amber-600 hover:to-amber-500 text-white font-semibold text-sm transition">
        <i class="fa-solid fa-rotate mr-2"></i> Recover Subscription
      </button>

    </div>
  </div>
</div>


{{-- ══════════ DIRECT REFUND MODAL ══════════ --}}
<div id="directRefundModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('directRefundModal').classList.add('hidden')"></div>

  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">

      <button onclick="document.getElementById('directRefundModal').classList.add('hidden')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 flex items-center justify-center transition">
        <i class="fa-solid fa-xmark"></i>
      </button>

      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
          <i class="fa-solid fa-rotate-left"></i>
        </div>
        <div>
          <h3 class="font-display text-lg font-bold text-slate-900">Direct Refund</h3>
          <p class="text-xs text-slate-400">Refund a payment directly by Payment ID</p>
        </div>
      </div>

      <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
          <i class="fa-solid fa-triangle-exclamation text-purple-500 mt-0.5 flex-shrink-0"></i>
          <div class="text-xs text-purple-700 leading-relaxed">
            Use when payment was captured but <strong>no subscription row exists</strong> in DB and you want to refund directly.
            Enter the <strong>Razorpay Payment ID</strong> (starts with <code class="bg-purple-100 px-1 rounded">pay_</code>).
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
          Razorpay Payment ID
        </label>
        <input type="text" id="directRefundPaymentId" placeholder="pay_XXXXXXXXXXXXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 font-mono focus:outline-none focus:ring-2 focus:ring-purple-100 focus:border-purple-300 transition">
      </div>

      <div id="directRefundResult" class="hidden mb-4 p-3 rounded-xl text-sm font-medium"></div>

      <button id="directRefundBtn" onclick="doDirectRefund()" class="w-full py-3 rounded-xl bg-gradient-to-br from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white font-semibold text-sm transition">
        <i class="fa-solid fa-rotate-left mr-2"></i> Refund Payment
      </button>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
  // ── Recover via row button (pre-fills payment ID if available) ──
  // ── Open recover modal ──
  function openRecoverModal(paymentId) {
    $('#recoverPaymentId').val(paymentId || '');
    $('#recoverResult').addClass('hidden').html('');
    document.getElementById('recoverModal').classList.remove('hidden');
  }


  // refund

  // ── Open direct refund modal ──
  function openDirectRefundModal(paymentId) {
    $('#directRefundPaymentId').val(paymentId || '');
    $('#directRefundResult').addClass('hidden').html('');
    document.getElementById('directRefundModal').classList.remove('hidden');
  }

  // ── Direct refund submit ──
  function doDirectRefund() {
    var paymentId = $('#directRefundPaymentId').val().trim();
    var $btn = $('#directRefundBtn');
    var $result = $('#directRefundResult');

    if (!paymentId) {
      $result.removeClass('hidden bg-emerald-50 text-emerald-700')
        .addClass('bg-rose-50 text-rose-700')
        .html('<i class="fa-solid fa-xmark mr-2"></i>Please enter a Payment ID.')
        .removeClass('hidden');
      return;
    }

    $btn.prop('disabled', true)
      .html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Processing...');
    $result.addClass('hidden');

    $.ajax({
      url: "{{ route('subscriptions.directRefund') }}",
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        payment_id: paymentId
      },
      success: function(res) {
        if (res.success) {
          $result.removeClass('hidden bg-rose-50 text-rose-700')
            .addClass('bg-emerald-50 text-emerald-700')
            .html('<i class="fa-solid fa-check mr-2"></i>' + res.message)
            .removeClass('hidden');
          setTimeout(function() {
            location.reload();
          }, 2000);
        } else {
          $result.removeClass('hidden bg-emerald-50 text-emerald-700')
            .addClass('bg-rose-50 text-rose-700')
            .html('<i class="fa-solid fa-xmark mr-2"></i>' + res.message)
            .removeClass('hidden');
        }
      },
      error: function(xhr) {
        $result.removeClass('hidden bg-emerald-50 text-emerald-700')
          .addClass('bg-rose-50 text-rose-700')
          .html('<i class="fa-solid fa-xmark mr-2"></i>' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong.'))
          .removeClass('hidden');
      },
      complete: function() {
        $btn.prop('disabled', false)
          .html('<i class="fa-solid fa-rotate-left mr-2"></i>Refund Payment');
      }
    });
  }

  // ── Recover submit ──
  function doRecover() {
    var paymentId = $('#recoverPaymentId').val().trim();
    var $btn = $('#recoverBtn');
    var $result = $('#recoverResult');

    if (!paymentId) {
      $result.removeClass('hidden bg-emerald-50 text-emerald-700')
        .addClass('bg-rose-50 text-rose-700')
        .html('<i class="fa-solid fa-xmark mr-2"></i>Please enter a Payment ID.')
        .removeClass('hidden');
      return;
    }

    $btn.prop('disabled', true)
      .html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Recovering...');
    $result.addClass('hidden');

    $.ajax({
      url: "{{ route('subscriptions.recover') }}",
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        payment_id: paymentId
      },
      success: function(res) {
        if (res.success) {
          $result.removeClass('hidden bg-rose-50 text-rose-700')
            .addClass('bg-emerald-50 text-emerald-700')
            .html('<i class="fa-solid fa-check mr-2"></i>' + res.message)
            .removeClass('hidden');
          setTimeout(function() {
            location.reload();
          }, 2000);
        } else {
          $result.removeClass('hidden bg-emerald-50 text-emerald-700')
            .addClass('bg-rose-50 text-rose-700')
            .html('<i class="fa-solid fa-xmark mr-2"></i>' + res.message)
            .removeClass('hidden');
        }
      },
      error: function(xhr) {
        $result.removeClass('hidden bg-emerald-50 text-emerald-700')
          .addClass('bg-rose-50 text-rose-700')
          .html('<i class="fa-solid fa-xmark mr-2"></i>' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong.'))
          .removeClass('hidden');
      },
      complete: function() {
        $btn.prop('disabled', false)
          .html('<i class="fa-solid fa-rotate mr-2"></i>Recover Subscription');
      }
    });
  }

  $(document).ready(function() {

    toastr.options = {
      positionClass: 'toast-top-right',
      timeOut: 3000,
      progressBar: true
    };

    // ── Generic AJAX action helper ──
    function ajaxAction(url, confirmMsg, $btn, onSuccess) {
      if (!confirm(confirmMsg)) return;
      $btn.prop('disabled', true).css('opacity', 0.5);

      $.ajax({
        url: url,
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(res) {
          if (res.success) {
            toastr.success(res.message);
            if (onSuccess) onSuccess();
            else location.reload();
          } else {
            toastr.error(res.message);
            $btn.prop('disabled', false).css('opacity', 1);
          }
        },
        error: function(xhr) {
          toastr.error(xhr.responseJSON?.message ?? 'Something went wrong.');
          $btn.prop('disabled', false).css('opacity', 1);
        }
      });
    }

    // ── Cancel ──
    $(document).on('click', '.cancel-btn', function() {
      ajaxAction(
        "{{ route('subscriptions.cancel',':id') }}".replace(':id', $(this).data('id')),
        'Cancel this subscription? This will immediately stop autopay on Razorpay and cannot be undone.',
        $(this)
      );
    });

    // ── Pause ──
    $(document).on('click', '.pause-btn', function() {
      ajaxAction(
        "{{ route('subscriptions.pause',':id') }}".replace(':id', $(this).data('id')),
        'Pause this subscription? Autopay will stop until resumed.',
        $(this)
      );
    });

    // ── Resume ──
    $(document).on('click', '.resume-btn', function() {
      ajaxAction(
        "{{ route('subscriptions.resume',':id') }}".replace(':id', $(this).data('id')),
        'Resume this subscription? Autopay will restart.',
        $(this)
      );
    });

    // ── Refund ──
    $(document).on('click', '.refund-btn', function() {
      ajaxAction(
        "{{ route('subscriptions.refund',':id') }}".replace(':id', $(this).data('id')),
        'Initiate a full refund for this payment? This cannot be undone.',
        $(this)
      );
    });

    // ── Complete ──
    $(document).on('click', '.complete-btn', function() {
      ajaxAction(
        "{{ route('subscriptions.complete',':id') }}".replace(':id', $(this).data('id')),
        'Mark as completed? User will lose premium access immediately.',
        $(this)
      );
    });

    // ── Delete ──
    $(document).on('click', '.delete-btn', function() {
      let id = $(this).data('id');
      let $btn = $(this);
      if (!confirm('Delete this record permanently? This cannot be undone.')) return;

      $.ajax({
        url: "{{ route('subscriptions.destroy',':id') }}".replace(':id', id),
        type: 'POST',
        data: {
          _method: 'DELETE',
          _token: '{{ csrf_token() }}'
        },
        success: function() {
          toastr.success('Record deleted');
          $btn.closest('tr').fadeOut(250, function() {
            $(this).remove();
          });
        },
        error: function() {
          toastr.error('Something went wrong!');
        }
      });
    });

    // ── Modal open/close ──
    function openModal() {
      $('#subModal').removeClass('hidden');
      requestAnimationFrame(function() {
        $('#subModalBackdrop').removeClass('opacity-0');
        $('#subModalPanel').removeClass('opacity-0 translate-y-3 scale-[0.98]');
      });
      $('body').css('overflow', 'hidden');
    }

    function closeModal() {
      $('#subModalBackdrop').addClass('opacity-0');
      $('#subModalPanel').addClass('opacity-0 translate-y-3 scale-[0.98]');
      $('body').css('overflow', '');
      setTimeout(function() {
        $('#subModal').addClass('hidden');
        $('#subModalContent').addClass('hidden').html('');
        $('#subModalLoading').removeClass('hidden');
      }, 200);
    }

    $('#subModalClose').click(closeModal);
    $('#subModalBackdrop').click(closeModal);
    $(document).keydown(function(e) {
      if (e.key === 'Escape' && !$('#subModal').hasClass('hidden')) closeModal();
    });

    // ── Status badge JS ──
    function statusBadge(status) {
      const map = {
        'active': ['emerald', 'Active'],
        'expired': ['amber', 'Expired'],
        'cancelled': ['rose', 'Cancelled'],
        'halted': ['red', 'Halted'],
        'paused': ['yellow', 'Paused'],
        'payment_failed': ['orange', 'Payment Failed'],
        'refund_initiated': ['purple', 'Refund Initiated'],
        'refunded': ['slate', 'Refunded'],
        'completed': ['teal', 'Completed'],
      };
      const [color, label] = map[status] || ['slate', 'Unknown'];
      return `<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-${color}-700 bg-${color}-50 border border-${color}-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-${color}-500"></span> ${label}
                </span>`;
    }

    function historyDot(status) {
      const dots = {
        cancelled: 'bg-rose-400',
        paid: 'bg-emerald-400'
      };
      return `<span class="w-2 h-2 rounded-full ${dots[status] || 'bg-slate-300'}"></span>`;
    }

    // ── View modal ──
    $(document).on('click', '.view-btn', function() {
      let id = $(this).data('id');
      openModal();

      $.ajax({
        url: "{{ route('subscriptions.show',':id') }}".replace(':id', id),
        type: 'GET',
        dataType: 'json',
        success: function(res) {
          if (!res.success) {
            toastr.error('Could not load details');
            closeModal();
            return;
          }

          const sub = res.subscription;
          const user = res.user;
          const history = res.history;

          let historyHtml = '';
          history.forEach(function(h) {
            historyHtml += `
                        <div class="flex items-center justify-between gap-3 py-3 ${h.is_current ? 'bg-indigo-50/60 -mx-3 px-3 rounded-xl' : ''}">
                          <div class="flex items-center gap-3">
                            ${historyDot(h.status)}
                            <div>
                              <div class="text-sm font-medium text-slate-700">
                                ${h.plan_label}
                                ${h.is_current ? '<span class="text-[10px] font-bold text-indigo-500 bg-indigo-100 px-1.5 py-0.5 rounded ml-1">CURRENT</span>' : ''}
                              </div>
                              <div class="text-xs text-slate-400 font-mono">${h.start_date} → ${h.expiry_date}</div>
                            </div>
                          </div>
                          <div class="text-sm font-mono font-semibold text-slate-600">₹${Number(h.amount).toLocaleString()}</div>
                        </div>`;
          });

          const html = `
                  <div class="p-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                      <div class="w-14 h-14 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold ring-4 ring-indigo-100 flex-shrink-0">
                        ${(user.name||'U').trim().charAt(0).toUpperCase()}
                      </div>
                      <div class="flex-1 min-w-0">
                        <h3 class="font-display text-lg font-bold text-slate-900 truncate">${user.name}</h3>
                        <div class="text-sm text-slate-400 truncate">${user.email || user.mobile || '—'}</div>
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
                        ${sub.status === 'active' && sub.days_left <= 5
                            ? `<div class="text-[11px] text-amber-600 font-semibold mt-1">${sub.days_left}d left</div>`
                            : ''}
                      </div>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4 mb-6">
                      <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Razorpay Details</div>
                      <div class="space-y-2">
                        <div class="flex items-center justify-between gap-3">
                          <span class="text-xs text-slate-400">Subscription ID</span>
                          <span class="font-mono text-xs text-slate-600 truncate max-w-[220px]" title="${sub.razorpay_subscription_id||''}">${sub.razorpay_subscription_id||'—'}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                          <span class="text-xs text-slate-400">Payment ID</span>
                          <span class="font-mono text-xs text-slate-600 truncate max-w-[220px]" title="${sub.razorpay_payment_id||''}">${sub.razorpay_payment_id||'—'}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                          <span class="text-xs text-slate-400">Created</span>
                          <span class="font-mono text-xs text-slate-600">${sub.created_at||'—'}</span>
                        </div>
                      </div>
                    </div>

                    <div>
                      <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-300"></i> Subscription History
                      </div>
                      <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto pr-1">
                        ${historyHtml || '<div class="text-sm text-slate-400 py-4 text-center">No history found.</div>'}
                      </div>
                    </div>
                  </div>`;

          $('#subModalContent').html(html);
          $('#subModalLoading').addClass('hidden');
          $('#subModalContent').removeClass('hidden');
        },
        error: function() {
          toastr.error('Could not load details');
          closeModal();
        }
      });
    });



    // ── Row icon click — pre-fills payment ID ──
    $(document).on('click', '.recover-modal-btn', function() {
      var paymentId = $(this).data('paymentid') || '';
      openRecoverModal(paymentId);
    });

  });
</script>

@endsection
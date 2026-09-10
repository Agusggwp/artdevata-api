@extends('layouts.admin')

@section('title', 'Edit Lead Prospek')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-800">Edit Lead Prospek #{{ $lead->id }}</h1>
      <p class="text-xs text-slate-500">Ubah data dan perkembangan status prospek {{ $lead->name }}.</p>
    </div>
    <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
      <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
  </div>

  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100">
    <form method="POST" action="{{ route('admin.leads.update', $lead->id) }}" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Prospek *</label>
          <input type="text" name="name" value="{{ old('name', $lead->name) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan / Instansi</label>
          <input type="text" name="company_name" value="{{ old('company_name', $lead->company_name) }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
          <input type="email" name="email" value="{{ old('email', $lead->email) }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
          <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Sumber Lead *</label>
          <select name="source" required class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
            <option value="website" {{ old('source', $lead->source) == 'website' ? 'selected' : '' }}>Website</option>
            <option value="whatsapp" {{ old('source', $lead->source) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
            <option value="instagram" {{ old('source', $lead->source) == 'instagram' ? 'selected' : '' }}>Instagram</option>
            <option value="tiktok" {{ old('source', $lead->source) == 'tiktok' ? 'selected' : '' }}>TikTok</option>
            <option value="facebook" {{ old('source', $lead->source) == 'facebook' ? 'selected' : '' }}>Facebook</option>
            <option value="referral" {{ old('source', $lead->source) == 'referral' ? 'selected' : '' }}>Referral</option>
            <option value="walk_in" {{ old('source', $lead->source) == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
            <option value="other" {{ old('source', $lead->source) == 'other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Layanan Yang Diminati</label>
          <input type="text" name="service_interest" value="{{ old('service_interest', $lead->service_interest) }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Budget (Rp)</label>
          <input type="number" step="0.01" name="estimated_budget" value="{{ old('estimated_budget', $lead->estimated_budget) }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Status Prospek *</label>
          <select name="status" required class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
            <option value="new" {{ old('status', $lead->status) == 'new' ? 'selected' : '' }}>New</option>
            <option value="contacted" {{ old('status', $lead->status) == 'contacted' ? 'selected' : '' }}>Contacted</option>
            <option value="qualified" {{ old('status', $lead->status) == 'qualified' ? 'selected' : '' }}>Qualified</option>
            <option value="negotiation" {{ old('status', $lead->status) == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
            <option value="won" {{ old('status', $lead->status) == 'won' ? 'selected' : '' }}>Won</option>
            <option value="lost" {{ old('status', $lead->status) == 'lost' ? 'selected' : '' }}>Lost</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Assigned Staff</label>
          <select name="assigned_to" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
            <option value="">-- Pilih Staff --</option>
            @foreach($admins as $admin)
              <option value="{{ $admin->id }}" {{ old('assigned_to', $lead->assigned_to) == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Jadwal Next Follow Up</label>
          <input type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', $lead->next_follow_up_at ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Prospek</label>
        <textarea name="address" rows="2" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">{{ old('address', $lead->address) }}</textarea>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
        <textarea name="notes" rows="3" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">{{ old('notes', $lead->notes) }}</textarea>
      </div>

      <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
        <a href="{{ route('admin.leads.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">Batal</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-sm transition-all">Perbarui Lead Prospek</button>
      </div>
    </form>
  </div>

</div>
@endsection

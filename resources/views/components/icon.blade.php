@props(['name', 'class' => ''])

@if($name === 'dashboard')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8v-10h-8v10zm0-18v6h8V3h-8z"/></svg>
@elseif($name === 'patients')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a7.5 7.5 0 0 1 13 0"/></svg>
@elseif($name === 'appointments')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
@elseif($name === 'pharmacy')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V3h8v4"/></svg>
@elseif($name === 'inventory')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg>
@elseif($name === 'billing')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M12 11v6"/></svg>
@elseif($name === 'lab')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2v6l-2 4v6a6 6 0 0 0 12 0v-6l-2-4V2"/></svg>
@elseif($name === 'notifications')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0v5a2 2 0 0 1-2 2h16a2 2 0 0 1-2-2V8z"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
@elseif($name === 'messages')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg>
@elseif($name === 'reporting')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17v-6M15 17v-2"/></svg>
@elseif($name === 'admin')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a7.5 7.5 0 0 1 13 0"/></svg>
@elseif($name === 'hr')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a7.5 7.5 0 0 1 13 0"/></svg>
@elseif($name === 'roles')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a7.5 7.5 0 0 1 13 0"/></svg>
@elseif($name === 'audit')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17v-6M15 17v-2"/></svg>
@elseif($name === 'requisitions')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h10l3 3v15H4V3h3z"/><path d="M8 10h8M8 14h8M8 18h5"/></svg>
@elseif($name === 'timesheets')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 10h18M8 14h3M8 18h8"/></svg>
@elseif($name === 'documents')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg>
@elseif(in_array($name, ['income', 'sales', 'payroll'], true))
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 15h5M16 9v6M14 11l2-2 2 2"/></svg>
@elseif(in_array($name, ['expenses', 'utilities'], true))
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h5M16 9v6M14 13l2 2 2-2"/></svg>
@else
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
@endif

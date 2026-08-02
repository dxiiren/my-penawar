<?php
// Shared <head> assets for the modernized UI (2026 facelift).
// The including page may set $pageTitle before the include.
// Static .html pages cannot include this file — they carry an identical inline copy.
$pageTitle = $pageTitle ?? 'Poliklinik Penawar';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="icon" type="image/png" href="image/2.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
      },
    },
  };
</script>
<style type="text/tailwindcss">
  .btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2; }
  .btn-outline { @apply inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-teal-400 hover:text-teal-700; }
  .field { @apply block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100; }
  .card { @apply rounded-2xl border border-slate-200 bg-white shadow-sm; }
  .eyebrow { @apply text-xs font-bold uppercase tracking-[0.2em] text-teal-600; }
  /* Tables echoed by the legacy PHP blocks (profile pages, booking slot matrix). */
  .styled-table { border-collapse: separate; border-spacing: 0; @apply w-full overflow-hidden rounded-xl border border-slate-200 bg-white text-left text-sm shadow-sm; }
  .styled-table th { @apply border-b border-slate-100 bg-teal-50/70 px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-teal-900; }
  .styled-table td { @apply border-b border-slate-100 px-5 py-3.5 text-slate-700; }
  .styled-table tr:last-child th, .styled-table tr:last-child td { border-bottom: 0; }
  /* Monthly report table (echoed with class="styled-table2"). */
  .styled-table2 { border-collapse: separate; border-spacing: 0; @apply mx-auto w-full max-w-4xl overflow-hidden rounded-xl border border-slate-200 bg-white text-sm shadow-sm; }
  .styled-table2 th { @apply bg-teal-700 px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-white; }
  .styled-table2 td { @apply border-b border-slate-100 px-5 py-3 text-center text-slate-700; }
  .styled-table2 tr:last-child td { border-bottom: 0; }
  /* CSS :target modal used by the profile pages' "Edit Profile" popup. */
  .overlay { @apply invisible fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 opacity-0 transition-opacity; }
  .overlay:target { @apply visible opacity-100; }
  /* Generic wrapper for admin/history list tables. */
  .data-table table { border-collapse: separate; border-spacing: 0; @apply w-full overflow-hidden rounded-xl border border-slate-200 bg-white text-sm shadow-sm; }
  .data-table th { @apply bg-teal-700 px-4 py-3.5 text-xs font-bold uppercase tracking-wider text-white; }
  .data-table td { @apply border-b border-slate-100 px-4 py-3 text-center text-slate-700; }
  .data-table tbody tr:hover { @apply bg-teal-50/40; }
</style>

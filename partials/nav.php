<?php
// Shared top navigation (2026 facelift). Optional vars set before the include:
//   $active      — 'home' | 'about' | 'contact' | 'team' | 'profile'
//   $profileHref — Profile link target on logged-in pages (e.g. 'patient profile.php')
// Static .html pages cannot include this file — they carry an identical inline copy.
$active      = $active ?? '';
$profileHref = $profileHref ?? null;
$navLinks = [
    ['home',    'index.html',   'Home'],
    ['about',   'aboutus.html', 'About Us'],
    ['contact', 'contact.html', 'Contact'],
    ['team',    'member.html',  'Our Team'],
];
if ($profileHref !== null) {
    $navLinks[] = ['profile', $profileHref, 'My Profile'];
}
?>
<header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur">
  <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
    <a href="index.html" class="flex shrink-0 items-center" aria-label="MyPenawar home">
      <img src="image/2.png" alt="MyPenawar — your best health consultant" class="h-10 w-auto">
    </a>
    <nav class="hidden items-center gap-1 md:flex" aria-label="Main navigation">
      <?php foreach ($navLinks as [$key, $href, $label]): ?>
      <a href="<?= htmlspecialchars($href) ?>" class="rounded-lg px-3 py-2 text-sm font-medium transition <?= $active === $key ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>"><?= htmlspecialchars($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="flex items-center gap-2">
      <a href="login.php" class="btn-primary hidden sm:inline-flex"><i class="fa-regular fa-calendar-check"></i> Book Appointment</a>
      <button type="button" id="navToggle" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-300 text-slate-600 md:hidden" aria-label="Toggle menu" aria-expanded="false">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </div>
  <nav id="navMobile" class="hidden border-t border-slate-200 bg-white px-4 py-3 md:hidden" aria-label="Mobile navigation">
    <?php foreach ($navLinks as [$key, $href, $label]): ?>
    <a href="<?= htmlspecialchars($href) ?>" class="block rounded-lg px-3 py-2 text-sm font-medium <?= $active === $key ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50' ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
    <a href="login.php" class="btn-primary mt-2 w-full"><i class="fa-regular fa-calendar-check"></i> Book Appointment</a>
  </nav>
</header>
<script>
  document.getElementById('navToggle').addEventListener('click', function () {
    var menu = document.getElementById('navMobile');
    var open = menu.classList.toggle('hidden') === false;
    this.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
</script>

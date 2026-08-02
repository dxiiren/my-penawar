<?php
    if(isset($_SESSION['message'])) :
?>

    <div class="mb-6 flex items-start justify-between gap-4 rounded-xl border border-amber-300 bg-amber-50 px-5 py-4 text-sm text-amber-900" role="alert">
        <p><i class="fa-solid fa-circle-info mr-2"></i><strong>Hey!</strong> <?= $_SESSION['message']; ?></p>
        <button type="button" class="text-lg font-bold leading-none text-amber-500 transition hover:text-amber-700" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
    </div>

<?php
    unset($_SESSION['message']);
    endif;
?>

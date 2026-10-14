<?php
$showUploadBlockedModal = !empty($_SESSION['invoice_upload_blocked']);
unset($_SESSION['invoice_upload_blocked']);
?>
<dialog id="invoiceUploadDisabledModal" aria-labelledby="invoiceUploadDisabledTitle" aria-describedby="invoiceUploadDisabledMessage" class="rounded-2xl p-0 w-full max-w-md shadow-2xl">
    <div class="bg-amber-500 text-white p-6">
        <h2 id="invoiceUploadDisabledTitle" class="text-xl font-bold">Carga de factura deshabilitada</h2>
    </div>
    <div class="p-6">
        <p id="invoiceUploadDisabledMessage" class="text-gray-700">No se encuentra habilitada la opción de subir factura. Contacte al administrador para solicitar la habilitación.</p>
        <form method="dialog" class="mt-6 text-right">
            <button autofocus class="bg-amber-500 hover:bg-amber-600 text-white rounded-xl px-5 py-2 font-semibold">Entendido</button>
        </form>
    </div>
</dialog>
<style>
    #invoiceUploadDisabledModal::backdrop { background: rgba(0, 0, 0, 0.5); }
</style>
<script>
    const invoiceUploadDisabledModal = document.getElementById('invoiceUploadDisabledModal');
    document.querySelectorAll('[data-invoice-upload-blocked]').forEach(button => {
        button.addEventListener('click', () => invoiceUploadDisabledModal.showModal());
    });
    <?php if ($showUploadBlockedModal): ?>
    invoiceUploadDisabledModal.showModal();
    <?php endif; ?>
</script>

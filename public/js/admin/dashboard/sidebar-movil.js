window.addEventListener('load', function() {

    const sidebar = document.getElementById('sidebar');
    document.getElementById('btnToggleSidebar')?.addEventListener('click', ()=> sidebar.classList.toggle('show'));

});

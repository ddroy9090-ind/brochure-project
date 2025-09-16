<script>
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    const closeBtn = document.getElementById('closeBtn');
    const userMenu = document.getElementById('userMenu');
    const dropdownMenu = document.getElementById('dropdownMenu');

    // ✅ Function to set initial state based on screen size
    function setInitialSidebar() {
        if (window.innerWidth <= 768) {
            // Mobile: Sidebar collapsed by default (icons only)
            sidebar.classList.add('collapsed');
            sidebar.classList.remove('expanded');
            menuBtn.classList.remove('hide');
            closeBtn.classList.add('hide');
        } else {
            // Desktop: Sidebar expanded by default
            sidebar.classList.add('expanded');
            sidebar.classList.remove('collapsed');
            menuBtn.classList.add('hide');
            closeBtn.classList.remove('hide');
        }
    }

    // ✅ Toggle open/close
    menuBtn.addEventListener('click', () => {
        sidebar.classList.add('expanded');
        sidebar.classList.remove('collapsed');
        menuBtn.classList.add('hide');
        closeBtn.classList.remove('hide');
    });

    closeBtn.addEventListener('click', () => {
        sidebar.classList.remove('expanded');
        sidebar.classList.add('collapsed');
        closeBtn.classList.add('hide');
        menuBtn.classList.remove('hide');
    });

    // ✅ Dropdown toggle
    userMenu.addEventListener('click', () => {
        dropdownMenu.classList.toggle('show');
    });

    // ✅ Close dropdown if clicked outside
    document.addEventListener('click', (e) => {
        if (!userMenu.contains(e.target)) {
            dropdownMenu.classList.remove('show');
        }
    });

    // ✅ On window resize, reset sidebar properly
    window.addEventListener('resize', setInitialSidebar);

    // ✅ Initial call
    setInitialSidebar();
</script>

<!-- JS for Progress Bar -->
<script>
    const fileInput = document.getElementById("fileUpload");
    const progressContainer = document.getElementById("progressContainer");
    const progressBar = document.getElementById("progressBar");

    fileInput.addEventListener("change", function () {
        if (fileInput.files.length > 0) {
            progressContainer.style.display = "block"; // show progress bar
            let progress = 0;

            // fake progress simulation (you can replace with real AJAX upload)
            let interval = setInterval(() => {
                progress += 10;
                progressBar.style.width = progress + "%";
                progressBar.innerText = progress + "%";
                progressBar.setAttribute("aria-valuenow", progress);

                if (progress >= 100) {
                    clearInterval(interval);
                    progressBar.innerText = "Upload Complete!";
                }
            }, 300);
        }
    });
</script>

<script>
    // URL-aware active state for sidebar
    (function () {
        const sidebar = document.querySelector('.cms-layout .sidebar');
        const items = sidebar.querySelectorAll('li');
        const links = sidebar.querySelectorAll('li a[href]');

        // Normalize to filename (handles /path/, ?query, #hash)
        const fileOf = (path) => {
            const p = path.split('?')[0].split('#')[0];
            const last = p.substring(p.lastIndexOf('/') + 1);
            return last || 'index.php'; // treat "/" as index.php
        };

        function setActiveFromUrl() {
            const current = fileOf(location.pathname);
            let matched = false;

            items.forEach(li => li.classList.remove('active'));

            links.forEach(link => {
                const linkFile = fileOf(new URL(link.getAttribute('href'), location.origin).pathname);
                if (linkFile === current) {
                    link.closest('li')?.classList.add('active');
                    matched = true;
                }
            });

            // Fallback: if nothing matched, keep first li active
            if (!matched && items.length) items[0].classList.add('active');
        }

        // Immediate visual feedback on click (before navigation)
        links.forEach(link => {
            link.addEventListener('click', () => {
                items.forEach(li => li.classList.remove('active'));
                link.closest('li')?.classList.add('active');
            });
        });

        setActiveFromUrl();
    })();
</script>



<script>
  // Optional: simple tab active state (no filtering logic here)
  document.querySelectorAll('.hh-docs .dl-tab').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      document.querySelectorAll('.hh-docs .dl-tab').forEach(b=>b.classList.remove('is-active'));
      btn.classList.add('is-active');
    });
  });
</script>


<script>
  // drag-over visuals
  document.querySelectorAll('.upload-input').forEach(inp=>{
    const box = inp.nextElementSibling;
    ['dragenter','dragover'].forEach(e=>box.addEventListener(e,ev=>{ev.preventDefault(); box.classList.add('is-dragover');}));
    ['dragleave','drop'].forEach(e=>box.addEventListener(e,ev=>{ev.preventDefault(); box.classList.remove('is-dragover');}));
    // show selected file name
    inp.addEventListener('change', ()=>{
      const nameBox = box.querySelector('.upload-name');
      if(!nameBox) return;
      nameBox.textContent = inp.files?.length ? Array.from(inp.files).map(f=>f.name).join(', ') : '';
    });
  });
</script>

<script>
    // Auto-dismiss alerts that opt-in via data-auto-dismiss attribute
    (function () {
        const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
        if (!alerts.length) {
            return;
        }

        alerts.forEach((alertEl) => {
            const attributeValue = alertEl.getAttribute('data-auto-dismiss');
            let delay = parseInt(attributeValue, 10);
            if (Number.isNaN(delay) || delay < 0) {
                delay = 5000;
            }

            window.setTimeout(() => {
                if (window.bootstrap && typeof window.bootstrap.Alert === 'function') {
                    const instance = window.bootstrap.Alert.getOrCreateInstance(alertEl);
                    instance.close();
                    return;
                }

                alertEl.classList.remove('show');
                alertEl.style.display = 'none';
            }, delay);
        });
    })();
</script>



</body>

</html>
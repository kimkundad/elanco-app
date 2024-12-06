<script src="{{ url('admin/js/lib/jquery.min.js') }}"></script>
    <script src="{{ url('admin/js/lib/owl.carousel.min.js') }}"></script>
    <script src="{{ url('admin/js/lib/svg4everybody.min.js') }}"></script>
    <script src="{{ url('admin/js/lib/apexcharts.min.js') }}"></script>
    <script src="{{ url('admin/js/lib/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ url('admin/js/app.js') }}"></script>
    <script src="{{ url('admin/js/charts.js') }}"></script>

    <script>

    const tabs = document.querySelectorAll('.tab');
const panes = document.querySelectorAll('.tab-pane');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    // Remove active class from all tabs
    tabs.forEach(t => t.classList.remove('active'));
    // Add active class to clicked tab
    tab.classList.add('active');

    // Hide all panes
    panes.forEach(pane => pane.classList.remove('active'));
    // Show corresponding pane
    const target = document.querySelector(`#${tab.dataset.tab}`);
    target.classList.add('active');
  });
});

    </script>

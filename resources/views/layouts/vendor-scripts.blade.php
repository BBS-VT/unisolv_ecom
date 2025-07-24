<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins.js') }}"></script>
<script>
    import CartNotifications from './cart-notifications';
    const cartNotifications = new CartNotifications();
</script>

@yield('script')

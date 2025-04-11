<ul id="account-panel" class="nav nav-pills flex-column" >
    <li class="nav-item">
        <a href="{{ route('account.profile') }}"  class="nav-link font-weight-bold" role="tab" aria-controls="tab-login" aria-expanded="false"><i class="fas fa-user-alt"></i> My Profile</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('account.orders') }}"  class="nav-link font-weight-bold" role="tab" aria-controls="tab-register" aria-expanded="false"><i class="fas fa-shopping-bag"></i>My Orders</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('account.wishlists') }}"  class="nav-link font-weight-bold" role="tab" aria-controls="tab-register" aria-expanded="false"><i class="fas fa-heart"></i> Wishlist</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('account.changePassword') }}"  class="nav-link font-weight-bold" role="tab" aria-controls="tab-register" aria-expanded="false"><i class="fas fa-lock"></i> Change Password</a>
    </li>
    {{-- <li class="nav-item">
        <a href="{{ route('account.logout') }}" class="nav-link font-weight-bold" role="tab" aria-controls="tab-register" aria-expanded="false"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li> --}}
    <li class="nav-item">
        <form id="logout-form" action="{{ route('account.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" class="nav-link font-weight-bold"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           role="tab" aria-controls="tab-register" aria-expanded="false">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </li>
    
</ul>

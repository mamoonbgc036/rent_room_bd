<form action="{{route('stripe')}}" method="POST">
    @csrf
    <button type="submit"> Pay With Stripe</button>
</form>

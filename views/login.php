<div class="auth-wrap">
    <div class="card auth-card">
        <h1>Sign in</h1>
        <p class="muted">Use the seeded admin account: <code>admin@example.com</code> / <code>admin123</code></p>
        <form method="POST" action="/login" class="form">
            <label>Email
                <input type="email" name="email" value="admin@example.com" required>
            </label>
            <label>Password
                <input type="password" name="password" value="admin123" required>
            </label>
            <button class="btn" type="submit">Login</button>
        </form>
    </div>
</div>

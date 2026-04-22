<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-5">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="text-center mb-4">Register Customer</h4>

                    <form method="POST" action="/register_user_customer">
                        {{ csrf_field() }}

                        <!-- Nama -->
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email aktif" required>
                        </div>

                        <!-- Kontak -->
                        <div class="mb-3">
                            <label class="form-label">Kontak</label>
                            <input type="number" name="kontak" class="form-control" placeholder="Kontak aktif" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                        </div>

                        <!-- Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                Register Customer
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <p class="text-center mt-3">
                Sudah punya akun? <a href="/login_customer">Login</a>
            </p>

        </div>
    </div>
</div>

</body>
</html>
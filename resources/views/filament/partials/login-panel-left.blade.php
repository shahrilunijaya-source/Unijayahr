<div class="hr-login-left" aria-hidden="true">
    {{-- Decorative circles --}}
    <div class="hr-login-circle hr-login-circle--1"></div>
    <div class="hr-login-circle hr-login-circle--2"></div>
    <div class="hr-login-circle hr-login-circle--3"></div>

    <div class="hr-login-left__body">
        {{-- Logo --}}
        <img src="{{ asset('logo_unijaya.png') }}" alt="Unijaya" style="height:3rem;width:auto;margin-bottom:2rem;filter:brightness(0) invert(1);">

        {{-- Badge --}}
        <div class="hr-login-badge">
            <span class="hr-login-badge__dot"></span>
            <span>Portal Dalaman</span>
        </div>

        {{-- Heading --}}
        <h1 class="hr-login-heading">Portal HR<br>Unijaya</h1>
        <p class="hr-login-sub">Sistem pengurusan sumber manusia eksklusif untuk warga Unijaya Resources Sdn Bhd.</p>

        {{-- Feature list --}}
        <ul class="hr-login-features">
            @foreach([
                'Profil pekerja dan direktori kakitangan',
                'Buku panduan dasar syarikat',
                'Penilaian prestasi KPI dwi-pihak',
                'Carta organisasi dan hierarki',
            ] as $feat)
                <li>
                    <span class="hr-login-features__dot"></span>
                    <span>{{ $feat }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <p class="hr-login-footer">Unijaya HR &copy; {{ date('Y') }} Unijaya Resources Sdn Bhd</p>
</div>

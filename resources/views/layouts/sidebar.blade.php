@php
    use App\Models\Rol;

@endphp

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <!-- Core Section -->

                
                <!-- Interface Section -->

                <a class="nav-link" href="{{ route('presupuestos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                    Presupuestos
                </a>

                @if ($rol_id = Auth::user()->rol_id == 4)
                    <a class="nav-link" href="{{ route('presupuestos.create') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>
                        Crear Presupuesto
                    </a>
                @endif
                @if ($rol_id = Auth::user()->rol_id == 99)
                    <a class="nav-link" href="{{ route('presupuestos.admin') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-lock"></i></div>
                        Administrar
                    </a>
                @endif
                @if ($rol_id = Auth::user()->rol_id == 4)
                    <a class="nav-link" href="{{ route('presupuestos.profesionales') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-doctor"></i></div>
                        Gestionar profesional
                    </a>
                @endif

            </div>
        </div>
        <div class="sb-sidenav-footer" style="display: flex; flex-direction:row; gap:15px">
            <div style="display: flex; flex-direction: column">
                <div class="small">Usuario:</div>
                {{ Auth::user()->name }}
            </div>
            <div style="display: flex; flex-direction: column">
                <div class="small">Area:</div>
                {{ Rol::find(Auth::user()->rol_id)->nombre }}
            </div>


        </div>
    </nav>
</div>

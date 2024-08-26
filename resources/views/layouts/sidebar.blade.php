<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <!-- Core Section -->
            
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <!-- Interface Section -->

                <a class="nav-link" href="{{ route('presupuestos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                    Presupuestos
                </a>
                
                <a class="nav-link" href="{{ route('presupuestos.create') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>
                    Crear Presupuesto
                </a>
                
                
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Usuario:</div>
            {{ Auth::user()->name }}
        </div>
    </nav>
</div>
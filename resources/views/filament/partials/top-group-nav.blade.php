<div class="hr-topnav" id="hr-topnav" aria-label="Navigation groups">
    @php
    $groups = [
        ['label' => 'Account',        'first' => '/app/my-profile'],
        ['label' => 'Resources',      'first' => '/app/handbook'],
        ['label' => 'People',         'first' => '/app/users'],
        ['label' => 'Leave',          'first' => '/app/leave-types'],
        ['label' => 'KPI',            'first' => '/app/kpi-rubric-templates'],
        ['label' => 'Attendance',     'first' => '/app/team-attendance-page'],
        ['label' => 'Personality',    'first' => '/app/personality-assessments'],
        ['label' => 'Management',     'first' => '/app/leave-approvals-page'],
        ['label' => 'Handbook Admin', 'first' => '/app/handbook-parts'],
    ];
    @endphp

    @foreach($groups as $g)
    <button class="hr-topnav-tab"
            data-group="{{ $g['label'] }}"
            data-first="{{ $g['first'] }}"
            type="button">
        {{ $g['label'] }}
    </button>
    @endforeach
</div>

<script>
(function () {
    var URL_GROUP = [
        ['/app/my-profile',              'Account'],
        ['/app/my-leave',                'Account'],
        ['/app/my-attendance',           'Account'],
        ['/app/handbook-parts',          'Handbook Admin'],
        ['/app/handbook',                'Resources'],
        ['/app/personality-test',        'Resources'],
        ['/app/my-suggestions',          'Resources'],
        ['/app/org-chart',               'Resources'],
        ['/app/users',                   'People'],
        ['/app/employee-profiles',       'People'],
        ['/app/staff-directory',         'People'],
        ['/app/staff-suggestions',       'People'],
        ['/app/leave-types',             'Leave'],
        ['/app/leave-balances',          'Leave'],
        ['/app/leave-requests',          'Leave'],
        ['/app/kpi-',                    'KPI'],
        ['/app/team-attendance',         'Attendance'],
        ['/app/attendances',             'Attendance'],
        ['/app/personality-assessments', 'Personality'],
        ['/app/personality-results',     'Personality'],
        ['/app/leave-approvals',         'Management'],
    ];

    function groupFromPath(path) {
        for (var i = 0; i < URL_GROUP.length; i++) {
            if (path.indexOf(URL_GROUP[i][0]) === 0) return URL_GROUP[i][1];
        }
        return localStorage.getItem('hr-active-group') || 'Account';
    }

    function applyGroup(activeGroup) {
        /* ── sidebar filtering ── */
        document.querySelectorAll('.fi-sidebar-group').forEach(function (grp) {
            var labelEl = grp.querySelector('.fi-sidebar-group-label');
            var label   = labelEl ? labelEl.textContent.trim() : '';
            if (label === activeGroup) {
                grp.style.display = '';
                /* hide the group heading row — shown in top nav */
                var hdr = grp.querySelector(':scope > button.fi-sidebar-group-button');
                if (hdr) hdr.style.display = 'none';
            } else {
                grp.style.display = 'none';
            }
        });

        /* ── tab highlight ── */
        document.querySelectorAll('.hr-topnav-tab').forEach(function (tab) {
            tab.classList.toggle('active', tab.dataset.group === activeGroup);
        });

        localStorage.setItem('hr-active-group', activeGroup);
    }

    function init() {
        /* Measure topbar height and position nav bar below it */
        var topbar = document.querySelector('.fi-topbar');
        if (topbar) {
            var topbarHeight = topbar.offsetHeight;
            var navBar = document.getElementById('hr-topnav');
            if (navBar) {
                navBar.style.top = topbarHeight + 'px';
                /* Adjust main content padding */
                var mainCtn = document.querySelector('.fi-main-ctn');
                if (mainCtn) {
                    mainCtn.style.paddingTop = (navBar.offsetHeight) + 'px';
                }
            }
        }
        applyGroup(groupFromPath(window.location.pathname));
    }

    /* tab clicks — DISABLED WHILE FIXING POSITIONING */
    /*
    document.addEventListener('click', function (e) {
        var tab = e.target.closest('.hr-topnav-tab');
        if (!tab) return;
        var group = tab.dataset.group;
        var first = tab.dataset.first;
        localStorage.setItem('hr-active-group', group);
        if (groupFromPath(window.location.pathname) === group) {
            applyGroup(group);
        } else {
            window.location.href = first;
        }
    });
    */

    /* initial run + after Livewire navigation */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    document.addEventListener('livewire:navigated', init);
    document.addEventListener('livewire:navigate',  init);

    /* Re-measure on window resize */
    window.addEventListener('resize', init);
})();
</script>

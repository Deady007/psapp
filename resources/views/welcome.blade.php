@extends('adminlte::master')

@section('title', config('branding.name', config('app.name', 'Laravel')))
@section('classes_body', 'landing-page')
@section('body_data', 'data-theme="softui" data-density="comfortable" data-motion="1" data-particles-intensity="med"')

@php
    $brandName = config('branding.name', config('app.name', 'MomentumSuite'));
    $brandTagline = config('branding.tagline', 'Customers / Projects / Delivery');
    $startHref = Route::has('register') ? route('register') : route('login');
    $loginHref = route('login');
@endphp

@section('body')
    <header class="nav">
        <div class="wrap">
            <div class="nav-inner">
                <a class="brand" href="{{ url('/') }}" aria-label="Home">
                    <div class="logo" aria-hidden="true"></div>
                    <div>
                        <strong>{{ $brandName }}</strong>
                        <div class="muted2" style="font-size:12px;margin-top:2px;">{{ $brandTagline }}</div>
                    </div>
                </a>

                <nav class="nav-links" aria-label="Primary">
                    <a href="#features">Features</a>
                    <a href="#solutions">Solutions</a>
                    <a href="#security">Security</a>
                    <a href="#pricing">Pricing</a>
                </nav>

                <div class="nav-actions">
                    <button class="btn icon-btn ghost" id="themeBtn" aria-label="Toggle theme">
                        <span aria-hidden="true">☾</span>
                    </button>
                    @auth
                        <a class="btn ghost" href="{{ route('dashboard') }}">Dashboard</a>
                    @else
                        <a class="btn ghost" href="{{ $loginHref }}">Sign In</a>
                        @if (Route::has('register'))
                            <a class="btn primary" href="{{ $startHref }}">Start Free</a>
                        @endif
                    @endauth
                    <button class="btn icon-btn hamburger" id="menuBtn" aria-label="Open menu">
                        <span aria-hidden="true">☰</span>
                    </button>
                </div>
            </div>

            <div id="mobileMenu" class="card inset" style="display:none; margin-bottom:14px; padding:10px;">
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    <a class="btn" href="#features">Features</a>
                    <a class="btn" href="#solutions">Solutions</a>
                    <a class="btn" href="#security">Security</a>
                    <a class="btn" href="#pricing">Pricing</a>
                </div>
            </div>
        </div>
    </header>

    <main id="top" class="wrap">
        <section class="hero">
            <div class="hero-grid">
                <div class="card inset hero-left">
                    <span class="pill"><span class="dot"></span><span class="muted">Premium SoftUI workspace</span></span>

                    <h1>Plan, track, and deliver with confidence.</h1>
                    <p class="sub">Premium workspace for customers, projects, and momentum - with Kanban flow, role-based access, and client-ready reporting.</p>

                    <div class="cta-row">
                        @if (Route::has('register'))
                            <a class="btn primary" href="{{ $startHref }}">Start Free</a>
                        @endif
                        <a class="btn" href="#tour">View Dashboard</a>
                    </div>

                    <div class="hero-meta">
                        <span class="pill"><span class="dot"></span><span class="muted">Setup in minutes</span></span>
                        <span class="pill"><span class="dot"></span><span class="muted">Roles and permissions</span></span>
                        <span class="pill"><span class="dot"></span><span class="muted">Reports clients appreciate</span></span>
                    </div>
                </div>

                <aside class="card inset snapshot" aria-label="Workspace Snapshot">
                    <div class="snap-top">
                        <div class="snap-title">
                            <strong>Workspace Snapshot</strong>
                            <span>Dashboard / Customers / Projects</span>
                        </div>
                        <span class="kbd">Ctrl or Cmd + K</span>
                    </div>

                    <div class="widgets">
                        <div class="widget">
                            <div class="label">Active Customers</div>
                            <div class="value">128</div>
                            <div class="spark"><i></i></div>
                        </div>
                        <div class="widget">
                            <div class="label">Live Projects</div>
                            <div class="value">24</div>
                            <div class="spark"><i style="width:52%"></i></div>
                        </div>
                        <div class="widget">
                            <div class="label">Delivery Pulse</div>
                            <div class="value">92%</div>
                            <div class="spark"><i style="width:78%"></i></div>
                        </div>
                    </div>

                    <div class="kanban">
                        <div class="kanban-head">
                            <strong style="font-size:13px;">Kanban Preview</strong>
                            <span class="pill" style="padding:6px 10px;">
                                <span class="dot"></span><span class="muted" style="font-size:12px;">Development / Testing</span>
                            </span>
                        </div>

                        <div class="cols">
                            <div class="col">
                                <h4>Development</h4>
                                <div class="task">
                                    <b>API status indicators</b>
                                    <div class="row">
                                        <span class="badge purple">In progress</span>
                                        <span class="muted2" style="font-size:11px;">ETA 2d</span>
                                    </div>
                                </div>
                                <div class="task">
                                    <b>Milestone budget sync</b>
                                    <div class="row">
                                        <span class="badge gold">Review</span>
                                        <span class="muted2" style="font-size:11px;">Owner: PM</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <h4>Testing</h4>
                                <div class="task">
                                    <b>Role permissions matrix</b>
                                    <div class="row">
                                        <span class="badge green">Ready</span>
                                        <span class="muted2" style="font-size:11px;">QA: 6 cases</span>
                                    </div>
                                </div>
                                <div class="task">
                                    <b>Client report template</b>
                                    <div class="row">
                                        <span class="badge purple">Polish</span>
                                        <span class="muted2" style="font-size:11px;">v1.2</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="card inset proof" aria-label="Social proof">
                <div class="left">
                    <span class="pill"><span class="dot"></span><span class="muted">Trusted by high-velocity teams.</span></span>
                    <div class="logos" aria-label="Logos">
                        <span class="logo-chip">NovaWorks</span>
                        <span class="logo-chip">PixelForge</span>
                        <span class="logo-chip">BlueLedger</span>
                        <span class="logo-chip">SprintLabs</span>
                    </div>
                </div>
                <div class="counters" aria-label="Counters">
                    <div class="counter"><b>1,240+</b><span>Teams onboarded</span></div>
                    <div class="counter"><b>18,500+</b><span>Projects delivered</span></div>
                    <div class="counter"><b>4.9/5</b><span>Client satisfaction</span></div>
                </div>
            </div>
        </section>

        <section id="features">
            <div class="section-head">
                <div>
                    <h2>Everything your delivery system needs</h2>
                    <p>From customers to projects to reporting - keep everyone aligned with premium visibility and control.</p>
                </div>
            </div>

            <div class="grid features">
                <article class="card inset feature">
                    <div class="ico" aria-hidden="true">👥</div>
                    <h3>Customers: all clients, one view</h3>
                    <p>Centralize contacts, context, and activity so nothing falls through the cracks.</p>
                </article>

                <article class="card inset feature">
                    <div class="ico" aria-hidden="true">📌</div>
                    <h3>Projects: health, milestones, budgets</h3>
                    <p>Track progress, scope, and risks with live indicators and clean timelines.</p>
                </article>

                <article class="card inset feature">
                    <div class="ico" aria-hidden="true">🧩</div>
                    <h3>Kanban: dev and test flow visibility</h3>
                    <p>Move work through stages with clarity - prioritize, unblock, and ship.</p>
                </article>

                <article class="card inset feature">
                    <div class="ico" aria-hidden="true">📊</div>
                    <h3>Reports: client-ready updates</h3>
                    <p>Generate status reports that look great and save hours every week.</p>
                </article>

                <article class="card inset feature">
                    <div class="ico" aria-hidden="true">🛡️</div>
                    <h3>Roles and permissions: granular control</h3>
                    <p>Lock down access by role, team, customer, or project with confidence.</p>
                </article>

                <article class="card inset feature">
                    <div class="ico" aria-hidden="true">⚡</div>
                    <h3>Dashboard: momentum at a glance</h3>
                    <p>See delivery pulse, blockers, and priorities instantly.</p>
                </article>
            </div>
        </section>

        <section id="solutions">
            <div class="section-head">
                <div>
                    <h2>A simple workflow that scales</h2>
                    <p>Keep setup lightweight, then grow into advanced dashboards, reporting, and governance as you scale.</p>
                </div>
            </div>

            <div class="grid workflow">
                <div class="card inset timeline">
                    <strong style="font-size:14px;">Step Timeline</strong>
                    <div class="steps" aria-label="Workflow steps">
                        <div class="step">
                            <div class="n">1</div>
                            <div>
                                <h4>Add customer</h4>
                                <p>Capture client details, stakeholders, and contracts in one place.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="n">2</div>
                            <div>
                                <h4>Create project</h4>
                                <p>Set goals, milestones, budgets, and delivery expectations.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="n">3</div>
                            <div>
                                <h4>Assign team</h4>
                                <p>Invite collaborators, define roles, and control permissions.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="n">4</div>
                            <div>
                                <h4>Track and report</h4>
                                <p>Run Kanban, monitor health, and publish client-ready updates.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="workflow-right" aria-label="Step cards">
                    <div class="vcard card inset">
                        <div class="row">
                            <strong>Customer: Acme Retail</strong>
                            <span class="badge green">Active</span>
                        </div>
                        <div class="muted2" style="font-size:12px; margin-top:6px;">Next review: Thu / Stakeholders: 4</div>
                    </div>

                    <div class="vcard card inset">
                        <div class="row">
                            <strong>Project: Mobile Revamp</strong>
                            <span class="badge purple">On track</span>
                        </div>
                        <div class="muted2" style="font-size:12px; margin-top:6px;">Milestone 3 / Budget used: 61%</div>
                        <div class="progress" aria-hidden="true"><i></i></div>
                    </div>

                    <div class="vcard card inset">
                        <div class="row">
                            <strong>Report: Weekly Status</strong>
                            <span class="badge gold">Draft</span>
                        </div>
                        <div class="muted2" style="font-size:12px; margin-top:6px;">Auto-filled KPIs / Export to PDF</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tour">
            <div class="section-head">
                <div>
                    <h2>Product tour</h2>
                    <p>Click through the core areas: dashboard, customers, projects, reports, and admin.</p>
                </div>
            </div>

            <div class="grid tour">
                <div class="tabs" role="tablist" aria-label="Product Tour Tabs">
                    <button class="tab active" role="tab" aria-selected="true" data-tab="dashboard">
                        Dashboard
                        <small>Live status indicators and progress</small>
                    </button>
                    <button class="tab" role="tab" aria-selected="false" data-tab="customers">
                        Customers
                        <small>Everything tied back to the client</small>
                    </button>
                    <button class="tab" role="tab" aria-selected="false" data-tab="projects">
                        Projects
                        <small>Milestones, budgets, health</small>
                    </button>
                    <button class="tab" role="tab" aria-selected="false" data-tab="reports">
                        Reports
                        <small>Client-ready updates in minutes</small>
                    </button>
                    <button class="tab" role="tab" aria-selected="false" data-tab="admin">
                        Admin
                        <small>Roles, permissions, audit trail</small>
                    </button>
                </div>

                <div class="card inset tour-pane" role="tabpanel" aria-live="polite">
                    <div id="paneTitle" style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                        <strong style="font-size:14px;">Dashboard overview</strong>
                        <span class="pill"><span class="dot"></span><span class="muted" style="font-size:12px;">Real-time</span></span>
                    </div>

                    <div class="mock" id="mockArea" aria-label="Mock UI">
                        <div class="topbar">
                            <div class="dots" aria-hidden="true"><i></i><i></i><i></i></div>
                            <span class="kbd">Workspace / Q4</span>
                        </div>
                        <div class="table" id="mockTable">
                            <div class="cell">Project</div><div class="cell">Health</div><div class="cell">ETA</div>
                            <div class="cell">Client Portal</div><div class="cell">On track</div><div class="cell">6d</div>
                            <div class="cell">Billing Revamp</div><div class="cell">At risk</div><div class="cell">12d</div>
                            <div class="cell">Report Automation</div><div class="cell">On track</div><div class="cell">3d</div>
                        </div>
                    </div>

                    <ul class="bullets" id="bulletList">
                        <li>Live status indicators and progress for every project</li>
                        <li>Delivery pulse highlights risks before they escalate</li>
                        <li>Quick filters by customer, owner, and stage</li>
                    </ul>

                    <div class="cta-row">
                        @if (Route::has('register'))
                            <a class="btn primary" href="{{ $startHref }}">Start Free</a>
                        @endif
                        <a class="btn" href="#pricing">See Pricing</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="security">
            <div class="section-head">
                <div>
                    <h2>Admin and security</h2>
                    <p>Granular access control for teams, customers, and projects so everyone sees exactly what they should.</p>
                </div>
            </div>

            <div class="grid split">
                <div class="card inset" style="padding:18px;">
                    <strong style="font-size:14px;">Role-based governance</strong>
                    <p class="muted" style="margin:8px 0 0;">Assign permissions by role, scope access by customer or project, and keep operations clean as you grow.</p>

                    <ul class="checklist" aria-label="Security checklist">
                        <li><span class="tick" aria-hidden="true">✓</span><div><b>Roles and permissions</b><div class="muted2" style="font-size:12px;margin-top:3px;">Granular access control for every module.</div></div></li>
                        <li><span class="tick" aria-hidden="true">✓</span><div><b>Audit-friendly changes</b><div class="muted2" style="font-size:12px;margin-top:3px;">Track critical actions with optional audit logs.</div></div></li>
                        <li><span class="tick" aria-hidden="true">✓</span><div><b>Secure sharing</b><div class="muted2" style="font-size:12px;margin-top:3px;">Client visibility without exposing internal details.</div></div></li>
                    </ul>
                </div>

                <div class="card inset" style="padding:18px;">
                    <div class="snap-top">
                        <div class="snap-title">
                            <strong>Admin panel mock</strong>
                            <span>Permissions / Teams / Access</span>
                        </div>
                        <span class="badge purple">Admin</span>
                    </div>

                    <div class="mock" style="margin-top:12px;">
                        <div class="topbar">
                            <div class="dots" aria-hidden="true"><i></i><i></i><i></i></div>
                            <span class="kbd">Permissions</span>
                        </div>
                        <div style="display:grid; gap:10px;">
                            <div class="cell">✅ Customers: View / Edit</div>
                            <div class="cell">✅ Projects: View / Edit / Delete</div>
                            <div class="cell">✅ Kanban: Move cards / Assign</div>
                            <div class="cell">✅ Reports: Export / Share</div>
                            <div class="cell">✅ Admin: Roles / Teams</div>
                        </div>
                    </div>

                    <div class="cta-row" style="margin-top:12px;">
                        <a class="btn" href="#faq">Read FAQ</a>
                        @if (Route::has('register'))
                            <a class="btn primary" href="{{ $startHref }}">Start Free</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="section-head">
                <div>
                    <h2>Results teams can feel</h2>
                    <p>Move from scattered updates to a single delivery rhythm - dashboards for the team, reports for the client.</p>
                </div>
            </div>

            <div class="grid testimonial">
                <div class="card inset quote">
                    <p>"We finally have one source of truth for customers, projects, and reporting. The Kanban flow plus permissions cut back on all the back-and-forth."</p>
                    <div class="who">
                        <div class="avatar" aria-hidden="true"></div>
                        <div>
                            <b>Riya Mehta</b>
                            <div class="muted2" style="font-size:12px;">Delivery Lead / High-velocity Studio</div>
                        </div>
                    </div>
                </div>

                <div class="metrics">
                    <div class="card inset metric">
                        <b>25%</b>
                        <span>Faster delivery with clear ownership and flow.</span>
                    </div>
                    <div class="card inset metric">
                        <b>2x</b>
                        <span>Report speed with client-ready templates.</span>
                    </div>
                    <div class="card inset metric">
                        <b>0</b>
                        <span>Lost tasks thanks to unified customer and project tracking.</span>
                    </div>
                    <div class="card inset metric">
                        <b>92%</b>
                        <span>Delivery pulse keeps risks visible early.</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing" aria-label="Pricing and Plans">
            <div class="section-head">
                <div>
                    <h2>Pricing that fits how you work</h2>
                    <p>Start light, then scale into team workflows, permissions, and reporting.</p>
                </div>
            </div>

            <div class="grid pricing">
                <div class="card inset plan">
                    <h3>Starter</h3>
                    <div class="muted2">For small teams and pilots</div>
                    <div class="price">$19 <span class="muted2" style="font-size:12px;">/ user / month</span></div>
                    <ul>
                        <li>Customers and projects</li>
                        <li>Basic Kanban</li>
                        <li>Standard reports</li>
                        <li>Email support</li>
                    </ul>
                    <a class="btn" href="{{ $startHref }}">Get Starter</a>
                </div>

                <div class="card inset plan featured" id="start">
                    <h3>Teams</h3>
                    <div class="muted2">For delivery teams at scale</div>
                    <div class="price">$39 <span class="muted2" style="font-size:12px;">/ user / month</span></div>
                    <ul>
                        <li>Advanced dashboards and filters</li>
                        <li>Roles and permissions</li>
                        <li>Client-ready exports</li>
                        <li>Priority support</li>
                    </ul>
                    <a class="btn primary" href="{{ $startHref }}">Start Teams</a>
                </div>
            </div>

            <div class="card inset" id="demo" style="margin-top:18px; padding:18px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <div>
                    <b style="font-size:14px;">Prefer a demo?</b>
                    <div class="muted2" style="font-size:12px; margin-top:4px;">See dashboards, Kanban, and reporting tailored to your workflow.</div>
                </div>
                <div class="cta-row" style="margin:0;">
                    <a class="btn" href="#tour">Book Demo</a>
                    @if (Route::has('register'))
                        <a class="btn primary" href="{{ $startHref }}">Start Free</a>
                    @endif
                </div>
            </div>
        </section>

        <section id="faq">
            <div class="section-head">
                <div>
                    <h2>FAQ</h2>
                    <p>Common questions about setup, security, and reporting.</p>
                </div>
            </div>

            <div class="grid faq" role="region" aria-label="Frequently Asked Questions">
                <details>
                    <summary>How long does setup take? <span class="chev" aria-hidden="true">⌄</span></summary>
                    <p>Most teams create their first customer and project in under 10 minutes. Imports can come later.</p>
                </details>
                <details>
                    <summary>What team sizes does this support? <span class="chev" aria-hidden="true">⌄</span></summary>
                    <p>From solo operators to multi-team delivery orgs. Roles and permissions help scale cleanly.</p>
                </details>
                <details>
                    <summary>How does data security work? <span class="chev" aria-hidden="true">⌄</span></summary>
                    <p>Role-based access keeps data scoped. Add audit-friendly logging for critical admin actions.</p>
                </details>
                <details>
                    <summary>Can I share reports with clients? <span class="chev" aria-hidden="true">⌄</span></summary>
                    <p>Yes, generate clean status reports and export or share without exposing internal workflow details.</p>
                </details>
                <details>
                    <summary>Do you support Kanban stages like dev and test? <span class="chev" aria-hidden="true">⌄</span></summary>
                    <p>Absolutely. Configure stages to match your delivery pipeline, including development, testing, QA, and release.</p>
                </details>
                <details>
                    <summary>Can admins control access per project or customer? <span class="chev" aria-hidden="true">⌄</span></summary>
                    <p>Yes, scope permissions by module and by workspace entities to keep collaboration precise.</p>
                </details>
            </div>
        </section>

        <footer>
            <div class="wrap">
                <div class="foot">
                    <div>
                        <div class="brand" style="margin-bottom:10px;">
                            <div class="logo" aria-hidden="true"></div>
                            <div>
                                <strong>{{ $brandName }}</strong>
                                <div class="muted2" style="font-size:12px;margin-top:2px;">Premium delivery workspace</div>
                            </div>
                        </div>
                        <div class="muted" style="max-width:46ch;">
                            Customers, projects, Kanban, and reports in one premium system that scales with your team.
                        </div>
                        <div class="fine">© <span id="year"></span> {{ $brandName }}. All rights reserved.</div>
                    </div>

                    <div>
                        <h4>Quick Links</h4>
                        <a href="#features">Features</a>
                        <a href="#tour">Product Tour</a>
                        <a href="#pricing">Pricing</a>
                        <a href="#faq">FAQ</a>
                    </div>

                    <div>
                        <h4>Company</h4>
                        <a href="#tour">Book Demo</a>
                        <a href="{{ $startHref }}">Get Started</a>
                        <a href="{{ $loginHref }}">Sign In</a>
                        <a href="#pricing">Contact</a>
                    </div>

                    <div>
                        <h4>Legal</h4>
                        <a href="#pricing">Terms</a>
                        <a href="#security">Privacy</a>
                        <a href="#security">Security</a>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script>
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (menuBtn) {
            menuBtn.addEventListener('click', () => {
                const isOpen = mobileMenu.style.display === 'block';
                mobileMenu.style.display = isOpen ? 'none' : 'block';
            });
        }

        const themeBtn = document.getElementById('themeBtn');
        const themeRoot = document.body;
        let light = false;
        themeBtn?.addEventListener('click', () => {
            light = !light;
            themeRoot.style.setProperty('--bg0', light ? '#f6f7fb' : '#070a12');
            themeRoot.style.setProperty('--bg1', light ? '#eef0f7' : '#0b1020');
            themeRoot.style.setProperty('--txt', light ? '#101426' : '#eaf0ff');
            themeRoot.style.setProperty('--muted', light ? 'rgba(16,20,38,0.72)' : 'rgba(234,240,255,0.72)');
            themeRoot.style.setProperty('--muted2', light ? 'rgba(16,20,38,0.58)' : 'rgba(234,240,255,0.58)');
            themeRoot.style.background =
                light
                    ? 'radial-gradient(1100px 600px at 20% -10%, rgba(124,92,255,0.22), transparent 60%), radial-gradient(900px 500px at 90% 10%, rgba(46,233,167,0.16), transparent 55%), radial-gradient(900px 500px at 50% 110%, rgba(88,215,255,0.14), transparent 60%), linear-gradient(180deg, var(--bg0), var(--bg1))'
                    : 'radial-gradient(1100px 600px at 20% -10%, rgba(124,92,255,0.45), transparent 60%), radial-gradient(900px 500px at 90% 10%, rgba(46,233,167,0.25), transparent 55%), radial-gradient(900px 500px at 50% 110%, rgba(88,215,255,0.18), transparent 60%), linear-gradient(180deg, var(--bg0), var(--bg1))';
            themeBtn.querySelector('span').textContent = light ? '☀' : '☾';
        });

        const tabs = Array.from(document.querySelectorAll('.tab'));
        const paneTitle = document.getElementById('paneTitle');
        const mockTable = document.getElementById('mockTable');
        const bulletList = document.getElementById('bulletList');

        const TOUR = {
            dashboard: {
                title: 'Dashboard overview',
                badge: 'Real-time',
                rows: [
                    ['Project', 'Health', 'ETA'],
                    ['Client Portal', 'On track', '6d'],
                    ['Billing Revamp', 'At risk', '12d'],
                    ['Report Automation', 'On track', '3d']
                ],
                bullets: [
                    'Live status indicators and progress for every project',
                    'Delivery pulse highlights risks before they escalate',
                    'Quick filters by customer, owner, and stage'
                ]
            },
            customers: {
                title: 'Customers hub',
                badge: 'Single view',
                rows: [
                    ['Customer', 'Tier', 'Next touch'],
                    ['Acme Retail', 'Gold', 'Thu'],
                    ['NovaWorks', 'Silver', 'Mon'],
                    ['PixelForge', 'Gold', 'Today']
                ],
                bullets: [
                    'All stakeholders, notes, and activity in one place',
                    'Link projects and reports directly to the customer',
                    'Never miss an update or renewal moment'
                ]
            },
            projects: {
                title: 'Projects control center',
                badge: 'Milestones',
                rows: [
                    ['Project', 'Budget', 'Milestone'],
                    ['Mobile Revamp', '61%', 'M3'],
                    ['Client Portal', '44%', 'M2'],
                    ['Analytics Rollout', '73%', 'M4']
                ],
                bullets: [
                    'Health, milestones, and budgets with clear indicators',
                    'Dependencies and blockers stay visible',
                    'Track deliverables end-to-end with ownership'
                ]
            },
            reports: {
                title: 'Reports that clients love',
                badge: 'Export-ready',
                rows: [
                    ['Report', 'Status', 'Share'],
                    ['Weekly Status', 'Draft', 'PDF'],
                    ['Milestone Update', 'Ready', 'Link'],
                    ['Executive Summary', 'Ready', 'PDF']
                ],
                bullets: [
                    'Auto-filled KPIs and progress summaries',
                    'Client-ready formats: PDF or share links',
                    'Save hours every week with templates'
                ]
            },
            admin: {
                title: 'Admin and permissions',
                badge: 'Governance',
                rows: [
                    ['Role', 'Access', 'Scope'],
                    ['Admin', 'All', 'Workspace'],
                    ['PM', 'Projects/Reports', 'Team'],
                    ['Client', 'Reports', 'Customer']
                ],
                bullets: [
                    'Granular roles and permissions per module',
                    'Scope visibility by customer and project',
                    'Audit-friendly controls as you scale'
                ]
            }
        };

        function renderTab(key) {
            const data = TOUR[key];
            if (!data) {
                return;
            }

            paneTitle.innerHTML = `
                <strong style="font-size:14px;">${data.title}</strong>
                <span class="pill"><span class="dot"></span><span class="muted" style="font-size:12px;">${data.badge}</span></span>
            `;

            mockTable.innerHTML = '';
            data.rows.flat().forEach((txt, idx) => {
                const div = document.createElement('div');
                div.className = 'cell';
                div.textContent = txt;

                if (idx < 3) {
                    div.style.color = 'rgba(234,240,255,0.80)';
                    div.style.borderColor = 'rgba(255,255,255,0.12)';
                    div.style.background = 'rgba(255,255,255,0.05)';
                }
                mockTable.appendChild(div);
            });

            bulletList.innerHTML = '';
            data.bullets.forEach((b) => {
                const li = document.createElement('li');
                li.textContent = b;
                bulletList.appendChild(li);
            });
        }

        tabs.forEach((t) => {
            t.addEventListener('click', () => {
                tabs.forEach((x) => {
                    x.classList.remove('active');
                    x.setAttribute('aria-selected', 'false');
                });
                t.classList.add('active');
                t.setAttribute('aria-selected', 'true');
                renderTab(t.dataset.tab);
            });
        });

        document.getElementById('year').textContent = new Date().getFullYear();

        document.querySelectorAll('#mobileMenu a').forEach((a) => {
            a.addEventListener('click', () => {
                mobileMenu.style.display = 'none';
            });
        });

        const faqs = Array.from(document.querySelectorAll('details'));
        faqs.forEach((d) =>
            d.addEventListener('toggle', () => {
                if (d.open) {
                    faqs.forEach((o) => {
                        if (o !== d) {
                            o.removeAttribute('open');
                        }
                    });
                }
            })
        );
    </script>
@endsection

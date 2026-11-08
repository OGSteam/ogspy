# OGSpy Mod Development Workflow

## Scope

Use this workflow for every module directory currently present in this OGSpy checkout:

`attaques`, `autoupdate`, `bthof`, `cdr`, `expedition`, `flottes`, `graviton`, `ogscalc`, `oversight`, `production`, `quimsonde`, `recycleurs`, `superapix`, `varally`, and `xtense`.

Module ownership and release handling can differ. Do not assume that every module has a separate upstream repository or is packaged in the same way.

## Keep Repositories Separate

The OGSpy root `.gitignore` ignores `mod/*` (except `mod/.htaccess`). A module checkout under `mod/<name>` is therefore available for local integration without being added to the OGSpy Git repository. Confirm repository boundaries before editing or committing:

```powershell
git status --short --branch
git -C mod/<name> status --short --branch
git check-ignore -v mod/<name>
```

For a module with its own upstream repository, make module changes in that repository:

```powershell
git clone https://github.com/<owner>/<module>.git mod/<name>
git -C mod/<name> switch -c <topic-branch>
```

If `mod/<name>` already contains files, do not clone over it. Clone to a sibling directory, inspect the existing files, and copy or link the module into the ignored integration path without overwriting user work.

Commit and open the module PR from the module repository, using Conventional Commits. Do not stage module files from the OGSpy root or add the module as a submodule unless the project explicitly changes this repository policy.

## Identify Ownership First

Before changing a module, establish its source of truth from its README, Git metadata, release instructions, or maintainers. If ownership is unclear, ask before creating a new repository or pushing changes. Keep module-specific agent instructions in that module's upstream repository; keep shared rules here so the same policy is not duplicated across every module.

## Local Integration

Use the ignored `mod/<name>` directory to test a module with a local OGSpy checkout. Check the module's `install.php`, `update.php`, and `uninstall.php` behavior when present, and verify any OGSpy hooks or data exchange that the module uses. Keep local database credentials and generated configuration out of commits.

If a change also requires an OGSpy core/API change, keep the core and module changes in their respective repositories and PRs. Link the related PRs and state the order required for deployment.

## Release Packaging

The release workflow currently checks out `ogsteam/mod-xtense` and `ogsteam/mod-autoupdate` into `mod/xtense` and `mod/autoupdate` from their `master` branches. Develop changes in those upstream repositories and let the packaging workflow fetch them; do not commit local snapshots into OGSpy. For other modules, verify their current packaging and distribution policy before assuming they are included in an OGSpy release.

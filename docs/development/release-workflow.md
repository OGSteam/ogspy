# OGSpy release workflow

OGSpy uses semantic versions without a `v` prefix, for example `4.1.0`.
Database migration identifiers remain independent from the application version.

## Development snapshots

Every push to `develop` validates and packages the application. The next version is
proposed from Conventional Commit messages since the latest stable tag:

- `fix:` and other changes increment the patch version;
- `feat:` increments the minor version;
- a breaking-change marker increments the major version.

The resulting snapshot is stored as a temporary GitHub Actions artifact for seven
days. It does not create a Git tag or a GitHub release.

## Stable release

1. Open and review the release pull request from `develop` to `master`.
2. Merge the release pull request only after its required checks pass.
3. Create an annotated `MAJOR.MINOR.PATCH` tag on the merged `master` commit.
4. Push the tag to GitHub.

For example:

```shell
git switch master
git pull --ff-only origin master
git tag -a 4.1.0 -m "OGSpy 4.1.0"
git push origin 4.1.0
```

The release workflow verifies that the tag is valid SemVer and points to a commit
on `master`. It then builds the package with that exact application version and
creates a draft GitHub release with generated release notes. Review and complete
the draft notes before publishing the release. The same tag also publishes the
versioned application and nginx container images.

The version committed in `install/version.php` describes the development tree.
Stable packages always receive their exact version from the release tag.

# ogspy-deploy

Private repository containing the GitHub Actions workflow that deploys [OGSpy](https://github.com/OGSteam/ogspy) to Infomaniak shared hosting.

A deployment is triggered by opening an issue with a specific form, and **requires manual approval from the owner** before any files are transferred.

---

## How it works

```
1. Open an issue using the "Deployment Request" template
        ↓
2. Workflow acknowledges the request (posts a comment on the issue)
        ↓
3. Workflow pauses — owner receives an approval notification
        ↓
4. Owner approves (or rejects) the deployment in the Actions tab
        ↓
5. Workflow downloads the requested OGSpy release from OGSteam/ogspy
        ↓
6. Files are deployed to Infomaniak via FTPS
   (config files, cache, and logs are never overwritten)
        ↓
7. CLI upgrader runs on the server via SSH: php install/upgrade_cli.php upgrade
        ↓
8. Result (success / failure / rejected) is posted as a comment and the issue is closed
```

---

## One-time setup

### 1 — Required repository secrets

Go to **Settings → Secrets and variables → Actions** and add the following secrets:

| Secret | Description |
|---|---|
| `OGSPY_GITHUB_TOKEN` | Fine-grained PAT with **read** access to releases on `OGSteam/ogspy` |
| `INFOMANIAK_FTP_HOST` | FTP hostname provided by Infomaniak (e.g. `ftp.clusterXX.hosting.infomaniak.ch`) |
| `INFOMANIAK_FTP_USER` | FTP username |
| `INFOMANIAK_FTP_PASSWORD` | FTP password |
| `INFOMANIAK_DEPLOY_PATH` | Absolute remote path to the OGSpy root (e.g. `/sites/mysite.com/public_html/`) |
| `INFOMANIAK_SSH_HOST` | SSH hostname (same host or dedicated SSH entry point from Infomaniak panel) |
| `INFOMANIAK_SSH_USER` | SSH username |
| `INFOMANIAK_SSH_KEY` | SSH **private key** (PEM format, Ed25519 or RSA) |

#### Generating the GitHub PAT (`OGSPY_GITHUB_TOKEN`)

1. Go to <https://github.com/settings/personal-access-tokens/new>
2. Choose **Fine-grained token**
3. Resource owner: `OGSteam`
4. Repository access: Only `OGSteam/ogspy`
5. Permissions → Repository permissions → **Contents**: Read-only
6. Copy the token and save it as `OGSPY_GITHUB_TOKEN`

#### Generating the SSH key pair

```bash
ssh-keygen -t ed25519 -C "ogspy-deploy@github-actions" -f ogspy_deploy_key
```

- Add the **public key** (`ogspy_deploy_key.pub`) to your Infomaniak SSH keys  
  (Infomaniak Manager → Hosting → SSH keys)
- Add the **private key** (`ogspy_deploy_key` file contents) as the `INFOMANIAK_SSH_KEY` secret

---

### 2 — GitHub Environment with approval gate

1. Go to **Settings → Environments → New environment**
2. Name it exactly: `infomaniak-production`
3. Under **Deployment protection rules**, enable **Required reviewers**
4. Add yourself (the owner) as a required reviewer
5. Save

This is what causes the workflow to pause and notify you before deploying.

---

### 3 — `deploy` label

1. Go to **Issues → Labels → New label**
2. Name: `deploy`
3. Color: choose any (e.g. `#0075ca`)
4. Save

The workflow only activates when this exact label is applied to an issue.

---

## Deploying

1. Click **Issues → New issue**
2. Select the **"Deployment Request"** template
3. Fill in the form (version is optional; leave blank to deploy the latest release)
4. Submit — the workflow starts immediately and posts an acknowledgement comment

**To approve:** go to the **Actions** tab, open the running workflow, and click **Review deployments → Approve**.

**To reject:** click **Review deployments → Reject** — the issue will be closed automatically.

---

## Files protected from overwrite

The FTP deploy step **never overwrites** the following remote files (they contain instance-specific configuration or runtime data):

- `config/id.php` — database connection
- `config/key.php` — encryption key
- `config/salt` — password salt
- `install/install.lock` — installation lock file
- `cache/**` — cached data
- `logs/**` — application logs

---

## Troubleshooting

| Symptom | Likely cause |
|---|---|
| Download step fails with "No release found" | No published (non-draft) release exists on OGSteam/ogspy; publish the draft first |
| FTP step fails with auth error | Check `INFOMANIAK_FTP_HOST`, `INFOMANIAK_FTP_USER`, `INFOMANIAK_FTP_PASSWORD` |
| SSH step fails with "Permission denied" | Public key not added to Infomaniak, or wrong `INFOMANIAK_SSH_USER` |
| `upgrade_cli.php` returns an error | Check the full SSH output in the Actions log; may be a PHP version or DB connectivity issue |
| Approval notification not received | Ensure you are set as a required reviewer on the `infomaniak-production` environment |

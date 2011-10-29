Git
===

* Create branch: `git checkout -b ma_branch`
* Delete remote branch: `git push origin :ma_branch_to_delete`
* Keep in sync with master:
    # `git fetch origin`
    # `git merge origin/master`
* Resolve merge conflicts
    1. fix issue
    1. `git add .`
    1. `git commit`
    1. save commit w/o message
* Use .gitignore to avoid checking in files unnecessary for the project
* Checkout individual file from external branch. The -- helps git avoid confusion by separating branch from paths). Also useful for git reset --hard on a single file. Note: this does not merge, ie history is lost: `git checkout <external branch> -- <path to file>`
* Diff w/ stash: `git diff stash@{0}`
* Diff two branches: `git diff <branch 1>..<branch 2>`, e.g., `git diff master..head`
* See branch details: `git branch -vv`
* Only show your commits: `git diff master..head --no-merges`
* Use gui to only show your commits: `git difftool master..head --no-merges`
* Run git stat against two branches: `git diff --name-status master..head`
* Unstage a single file: `git reset HEAD <file>`
* View a single file in a given commit: `git show <commit>:<path>`
* Revert single file to specific commit: `git checkout <hash> -- <path to file>`
* GitX gui; handy for live editing staged changes
* Determine if branch exists on remote repo: `git log <repo name>/<branch name>`
* Revision-specific blame: `git blame <sha> -- <path>`. Useful if global change assigns ownership for all lines to one user
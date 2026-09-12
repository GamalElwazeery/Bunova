# Bunova State OS

State OS is system #8 and cross-cutting. Its rule is: context is disposable; repository truth is reconstructable.

State snapshots/journal/checkpoints live under `.executionkit/` and are derived, ignored runtime artifacts. They may summarize current task, branch/HEAD, evidence pointers, blockers and next lifecycle action, but cannot create tasks, change dependencies or override `TODO.md`.

Checkpoint after canonical task-state transitions and meaningful handoffs. Rehydrate before resumed mutation whenever branch/HEAD/TODO/config drift is detected. Memory or external continuity tools are advisory only and must be revalidated against live repository truth; task state may never be stored as memory authority.

K00 acceptance requires an actual checkpoint/rehydrate cycle and Amnesia Test on a runtime-capable host. File presence alone is not evidence that these commands ran.

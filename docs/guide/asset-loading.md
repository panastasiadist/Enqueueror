# Asset Loading

Enqueueror considers each asset's location in the subdirectory hierarchy, its filename and the language it targets, to decide on the order according to which each asset's code is pushed to the browser:

```bash
├── Global assets (language agnostic)
    └── Assets by ascending directory depth 
        └── Assets in the same directory by ascending filename order
├── Global assets (language specific)
    └── Assets by ascending directory depth
        └── Assets in the same directory by ascending filename order
├── Content specific assets (language agnostic)
    └── Assets by ascending directory depth
        └── Assets in the same directory by ascending filename order
├── Content specific assets (language specific)
    └── Assets by ascending directory depth
        └── Assets in the same directory by ascending filename order
```

:::info
The loading order won't be respected for assets acting as dependencies. For example, if Asset 1 would normally be loaded before Asset 2, but Asset 2 is a dependency of Asset 1, then Asset 2 will be loaded before Asset 1.
:::

:::warning
The aforementioned rules should not be taken into account to guarantee the code execution path. The actual order according to which the code executes may be impacted by third-party plugins (such as optimization plugins) or may be changed in a future release of Enqueueror. Dependencies should be used as the best way to coordinate the code execution path.
:::


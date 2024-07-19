# Asset Ordering

Enqueueror considers each asset's location in the subdirectory hierarchy, its filename and the language it targets, to decide on the order according to which each asset's raw or invocation code will appear in the HTML document delivered to the browser:

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
The aforementioned order will not apply to assets acting as dependencies. For instance, if **Asset 1** would normally appear in the HTML document prior to **Asset 2**, but **Asset 2** is a dependency of **Asset 1**, then **Asset 2** will be placed in the HTML document before **Asset 1**.
:::

:::warning
- The aforementioned rules should not be relied upon to guarantee the code execution path. 
- The actual sequence of code execution may be affected by third-party plugins (such as optimization plugins), or may be changed in a future release of Enqueueror. 
- As a result, dependencies should be employed as the most effective way to coordinate the code execution path.
- Regarding external script assets, when using the **`async`** attribute, the actual code execution sequence is in no way guaranteed.
:::

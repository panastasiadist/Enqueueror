# Asset Header

An asset may include an optional header, i.e., a block comment providing details about the asset in **`key:value`** pairs. Enqueueror considers these pairs to provide additional, asset-specific functionalities. The header should be positioned at the beginning, before any actual code. The format of the header is as follows:

```
/*
 * Key1: Value1
 * Key2: Value2
 */
```

Currently only the **`Requires`** key is supported, used to inform Enqueueror about the dependencies required by the asset.
# Asset Header

An asset may contain an optional header, that is, a block comment specifying details about the asset in **`key:value`** pairs that are taken into account by Enqueueror to support additional functionality on a per-asset basis. The header should appear first, before any other actual code. The format of the header is as follows:

```
/*
 * Key1: Value1
 * Key2: Value2
 */
```

Currently **`Requires`** key is supported, which is used to inform Enqueueror about the dependencies required by the asset.

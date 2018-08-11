# TVCollector

This MODx extension will help you quickly access additional fields and greatly improve the performance of your site.

---


### To display the required field of the current page, use this:
```
[[+tvc.tv1]]
[[+tvc.tv2]]
[[+tvc.tv3]]
...
```

- **tvc** - this is the prefix
- **tv1** - this is the name of the additional field


---


### How to use it with getResources:
```
[[getResources?
  &parents=`0`
  &tpl=`@INLINE [[+properties.tvc.tv1]] [[+properties.tvc.tv2]] [[+properties.tvc.tv3]]`
]]
```

or in chunk:
```
[[+properties.tvc.tv1]]
[[+properties.tvc.tv2]]
[[+properties.tvc.tv3]]
...
```
It does not need to include additional fields for getResources, this will greatly speed up the selection.


---


### How to use it with fastField:
```
[[#1.prop.tvc.tv1]]
[[#1.prop.tvc.tv2]]
[[#1.prop.tvc.tv3]]
...
```


---


If you installed this extension on an old site or programmatically changed additional fields, just click this menu item and wait for the data to be updated.

![screenshot](screenshots/img-1.png)

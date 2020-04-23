# TVCollector

This MODx extension will help you access additional fields faster 
and significantly improve the performance of your large site.

---

### To display the TV field of a resource, use the placeholder:
```
[[+tvc.tv1]]
[[+tvc.one]]
[[+tvc.two]]
```
**tv1** - is the name of your TV field.

---

### To display the required field with getResources, use the placeholder:
```
[[getResources?
  &parents=`0`
  &tpl=`@INLINE [[+properties.tvc.tv1]] [[+properties.tvc.one]] [[+properties.tvc.two]]`
]]
```

or in the template chunk:
```
[[+properties.tvc.tv1]]
[[+properties.tvc.one]]
[[+properties.tvc.two]]
```

Thus, you do not need to enable the output of TV fields for getResources, 
this will significantly speed up the selection of resources.

---

### To display the required TV fields with FastField, use placeholders:
```
[[#1.prop.tvc.tv1]]
[[#1.prop.tvc.one]]
[[#1.properties.tvc.two]]
```

---

![screenshot](screenshots/menu.jpg)

If you installed this extension on an old site or programmatically changed additional fields,
or just want to update all the data, just click on the **"Update"** menu item and wait for the data to update.

If you decide to remove this extension, click on the **"Clear"** menu item and wait for the cleaning to complete. 
This will delete the collected data from your database.

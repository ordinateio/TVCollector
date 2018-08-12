--------------------
Plugin: TVCollector
--------------------

This extension will help you access additional fields in the fastest way and will greatly enhance the performance of your site.

To display the current resource field, use:
[[+tvc.tv1]]
[[+tvc.tv2]]
[[+tvc.tv3]]
...

To display the required field with getResources, use:
[[getResources?
  &parents=`0`
  &tpl=`@INLINE [[+properties.tvc.tv1]] [[+properties.tvc.tv2]] [[+properties.tvc.tv3]]`
]]
It does not need to include additional fields for getResources, this will greatly speed up the selection.


To display the required field with fastField, use:
[[#1.prop.tvc.tv1]]
[[#1.prop.tvc.tv2]]
[[#1.prop.tvc.tv3]]
...

See also: https://github.com/callisto2410/TVCollector

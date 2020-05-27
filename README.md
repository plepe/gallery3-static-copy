First create an offline copy of a Gallery3 page
```sh
wget --mirror --convert-links --adjust-extension --page-requisites --no-parent -e robots=off https://example.com/gallery
cd example.com/gallery
mv combined lib modules themes var sub/page/
for i in *html ; do php path/to/clean-gallery3-dump/clear.php $i ; done
```

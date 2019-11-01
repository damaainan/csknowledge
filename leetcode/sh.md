https://discuss.leetcode.com/uploads/files/1470150906153-2yxeznm.png


https://assets.leetcode.com
https://assets.leetcode-cn.com/aliyun-lc-upload
https://assets.leetcode-cn.com/aliyun-lc-uploads
https://s3-lc-upload.s3.amazonaws.com

awk '{print $0}' list.md | xargs -I[  sed -i 's@https://assets.leetcode.com/uploads@../../static@' [
awk '{print $0}' list.md | xargs -I[  sed -i 's@https://assets.leetcode-cn.com/aliyun-lc-upload/uploads@../../static@' [
awk '{print $0}' list.md | xargs -I[  sed -i 's@https://assets.leetcode-cn.com/aliyun-lc-uploads/uploads@../../static@' [
awk '{print $0}' list.md | xargs -I[  sed -i 's@https://s3-lc-upload.s3.amazonaws.com/uploads@../../static@' [
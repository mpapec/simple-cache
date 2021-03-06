PHP Safe cache
==============

read&amp;write content in concurrent environments (suitable for large amounts of data)

Caching content and generally reading and creating files requires some kind of locking mechanism.
Many libraries are lacking in this area as are not paying too much attention to properly implement 
_locking_ which leads to data corruption and/or higher cpu load times.

So what should be desired behavior when having hundreds of concurrent threads which are competing to 
read and write to a single file?

- only one publisher thread at the same time, the rest should be readers
- publisher has a duty to generate/publish new content when old expires
- new content generation may take some time, but it should not prevent readers from accessing current cache
- readers only read the content or wait for it if old has expired
- to minimize readers wait time (majority of threads!) cached content for publisher should expire sooner (5-15 sec)


TODO
====
- ~~write Cache Lite drop in replacement with examples~~
- finding alternative to flock() for shared and exclusive locks
- not tested under win32


BUGS
====


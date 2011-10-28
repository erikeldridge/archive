Security
========

* Sanitize all input from end-users to reduce XSS and DB corruption risks
* Sanitize all content sourced from end-users prior to display to reduce stored XSS risk
* Validate all actions affecting privated data to reduce risks to session-fixation
* Use dynamic config for secure settings
* Enumerate all 3rd-party assets hosted on example.com
* Build kill switch into features, so we can turn off compromised product w/o waiting for fix; works well w/ fail-to-zero pattern
* 2011 CWE/SANS Top 25 Most Dangerous Software Errors: http://cwe.mitre.org/top25/index.html
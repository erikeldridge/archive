Code review checklist

Credit @timtruman

- Is anything missing i18n?
- Do all the resource paths look sane?
- Stage it. Understand what it does. Try to break it.
- What else should be covered by tests that isn't?
- Are things escaped (and in the right context—JavaScript versus HTML, etc.)?
- Are things easy to read? Could this be confusing to a new hire 12 months from now?
- bin/swoop selenium --no-truncation --single-file=TEST/… (run the tests repeatedly to check for future flakiness)
- Are any varibles named something confusingly similar?
- Can clicking fast (before async call finish) cause issues?
- What happens if you do this twice or three times? Are things setup and torn down properly?
- Is the language clear and simple?
- Could anything be better? Should it be deferred and a ticket opened?

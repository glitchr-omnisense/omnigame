# Writing a provider

A provider is a package `omnigame/<name>`, namespace `Omnigame\<Name>\`, requiring
`omnisense/omnigame` and `symfony/http-client`, written as omnifilm's are: a factory extending
`ProviderFactory` (taking `?HttpClientInterface`), a provider implementing `ProviderInterface` and
the capability interfaces it has, honest `capabilities()` (a library or ownership offered only when
the key it needs is configured), calls through `Http\JsonClient`, answers read with `Http\Data`,
recorded answers in its tests (captured when no key is needed, written after the documentation
otherwise, and said so), a `BareTest` extending `Testing\BareTestCase`, a `LiveTest` in the group
`live` for what needs no key. Read only: no purchase, no key granted, nothing written.

## The providers' terms, in short

| Provider | Attribution | Limits and use |
|---|---|---|
| steam | Valve's trademarks; no implied endorsement; links to Valve not "nofollow" | Web API: 100,000 calls a day; Steam data about a player only as that player asks; a privacy policy for what is kept. The store's endpoints are not documented by Valve: a gentle pace, answers kept |
| gog | - | no published API terms: the store's own endpoints, a gentle pace, answers kept |
| itch | - | the server-side API's scopes; an e-mail must be verified before an ownership lookup by e-mail |
| igdb | IGDB.com | Twitch's developer agreement; 4 requests a second |
| rawg | an active hyperlink to RAWG on every page that shows its data | free up to 20,000 requests a month; commercial use free under 100,000 monthly active users |

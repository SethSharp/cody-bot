# 2025 September CodingLabs Upskill Challenge: AI

The focus for today was to do something with ai. Something that has fascinated me over the past couple months are MCPs.
Their ability to allow ai models to usee API endpoints looks super powerful.

# Summary
The main idea I had in my mind was to create a AI bot to do a preliminary review of a PR, but not just any ai/bot - one that
was focused on CodingLabs best practises and specific to Laravel.

The main software to help achieve this is [vizra-adk](https://github.com/vizra-ai/vizra-adk), which is a Laravel Open Source
project that allows you to implement MCPs and build support agents which can use these MCPs. With a prebuilt user interface,
the ai side was the main focus.

# Result
Made a basic AI bot which could use the github mcp to fetch prs and do a basic review of them (among other tools part of the github mcp).
I didn't get as much done as I would have liked as I mostly worked on understanding how to setup a mcp locally (docker) and understanding how the
package worked. Which meant I didn't have enough time to implement a slite feature for ai to be aware of company best practises.

# Troubles
1. MCPs were a little tricky to setup, mostly use Docker images, attempted to run one locally (With Slite), but couldn't get it to work
2. The main github mcp on docker was tricky to find, but turns out docker has a beta feature mcps so once figuring that out it got the ball rolling

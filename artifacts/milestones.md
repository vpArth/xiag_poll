Plan:  
 ☑ Router  
   Should select matched controller/action and inject dependencies
 - ApiController
   ☑ POST /poll
   ☑ POST /vote
   - GET /poll/{uuid}/results
 - UIController
   - createPollPage GET /
   - votePage       GET /{uuid}
 
   - twig templates for ui pages

 - SchemaManager  
   up/down project database schema
   
 - Some kind of installation script  

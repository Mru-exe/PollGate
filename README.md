# PollGate
PollGate semestral project @ CTU

DB Diagram:
```
Table Users {
  Id INT PK
  Username VARCHAR(32)
  PasswordSalt VARCHAR(128)
  PasswordHash VARCHAR(256)
  RoleId INT
}

Table Roles {
  Id INT PK
  Name VARCHAR(64)
  Permissions INT
}


Ref: Users.RoleId < Roles.Id
```
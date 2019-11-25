# Infra-Code
Terraform IaaC ECR and ECS Creation
* Need aws-cli to be installed
* Need docker service to be installed
* Need Terraform installed
* Need AWS secret and access for a user with required role access access to create ECR and ECS(Use IAM).
* First create ECR
* Second the ECR
** ECR Inputs Required **
* Access Key and Secret Key
* Region where ECR repository to be created
** ECR Output **
* Repository URL will be the output of ECR.(eg: 249****.dkr.ecr.us-east-2.amazonaws.com/test-proto)
* Our Image URL will be the add ":latest" at the end of repository URL (eg: 249****.dkr.ecr.us-east-2.amazonaws.com/test-proto:latest)
** ECS Inputs **
* Access Key and Secret Key
* Region where ECR repository to be created
* Image URL (which is repositoryurl:latest) (eg: 249****.dkr.ecr.us-east-2.amazonaws.com/test-proto:latest)
** ECS Output **
* ALB URL (application will be running in that url)

Commands: 
** terraform apply (for creation) **
** terraform destroy (for deletion) **

** NOTE: **
For ECS got terraform-ecs-fargate/terraform and run terraform apply or destroy as needed.

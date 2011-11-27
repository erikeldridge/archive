# This script accepts a github repo url, clones it, and merges it into the archive project

# Require project repo url as 1st arg
if [ ! $1 ]; then
  echo 'project url required, e.g., $ bash archiver.sh git@github.com:erikeldridge/CivicDB.git';
  exit 1;
fi

# Extract project name from repo url
project_name=$( ruby -e "print '$1'.scan(/(\w+)\.git/)[0][0]" );

# Clone project
git clone $1 ../$project_name;

# Define remote pointing at local copy of project
git remote rm merger
git remote add merger ../$project_name;

# Merge in project
git pull merger master

# Make directory to house project files
mkdir $project_name




set -e

USERNAME=
HOMEDIR=vision2020.loveburien.com

# mkdir
ssh $USERNAME@vision2020.loveburien.com -i ./id_rsa -o StrictHostKeyChecking=no "mkdir -p ~/$HOMEDIR/manual"

rsync -rlpvt \
	-e "ssh -i ./id_rsa -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o GlobalKnownHostsFile=/dev/null" \
	--exclude="id_rsa*" \
    --exclude='.git/' \
    --exclude='node_modules/' \
	--exclude='uploads/' \
	--delete \
	./app/ $USERNAME@vision2020.loveburien.com:~/$HOMEDIR/manual/

# change symlink

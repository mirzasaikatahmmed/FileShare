#!/bin/bash
# Cloudflare Tunnel Setup Script for FileShare Application

set -e

echo "================================================"
echo "Cloudflare Tunnel Setup for FileShare"
echo "================================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}Step 1: Installing Cloudflared...${NC}"
cd ~
curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o cloudflared
sudo chmod +x cloudflared
sudo mv cloudflared /usr/local/bin/
echo -e "${GREEN}✓ Cloudflared installed${NC}"
echo ""

echo -e "${YELLOW}Step 2: Authenticating with Cloudflare...${NC}"
echo "This will open a browser window. Please login to your Cloudflare account."
cloudflared tunnel login
echo -e "${GREEN}✓ Authenticated with Cloudflare${NC}"
echo ""

echo -e "${YELLOW}Step 3: Creating tunnel...${NC}"
cloudflared tunnel create fileshare
echo -e "${GREEN}✓ Tunnel created${NC}"
echo ""

echo -e "${YELLOW}Step 4: Getting tunnel ID...${NC}"
TUNNEL_ID=$(cloudflared tunnel list | grep fileshare | awk '{print $1}')
echo "Tunnel ID: $TUNNEL_ID"
echo ""

echo -e "${YELLOW}Step 5: Creating tunnel configuration...${NC}"
mkdir -p ~/.cloudflared

cat << EOF > ~/.cloudflared/config.yml
tunnel: $TUNNEL_ID
credentials-file: /root/.cloudflared/$TUNNEL_ID.json

ingress:
  - hostname: your-domain.com
    service: http://192.168.0.200:40080
    originRequest:
      noTLSVerify: true
  - service: http_status:404
EOF

echo -e "${GREEN}✓ Configuration created at ~/.cloudflared/config.yml${NC}"
echo ""

echo -e "${YELLOW}Step 6: Setting up DNS...${NC}"
echo "Please enter your domain name (e.g., fileshare.yourdomain.com):"
read DOMAIN_NAME

cloudflared tunnel route dns fileshare "$DOMAIN_NAME"
echo -e "${GREEN}✓ DNS route created for $DOMAIN_NAME${NC}"
echo ""

echo -e "${YELLOW}Step 7: Creating systemd service...${NC}"
sudo cat << EOF > /etc/systemd/system/cloudflared-fileshare.service
[Unit]
Description=Cloudflare Tunnel for FileShare
After=network.target

[Service]
Type=simple
User=root
ExecStart=/usr/local/bin/cloudflared tunnel --config /root/.cloudflared/config.yml run fileshare
Restart=on-failure
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable cloudflared-fileshare
sudo systemctl start cloudflared-fileshare
echo -e "${GREEN}✓ Cloudflared service created and started${NC}"
echo ""

echo -e "${YELLOW}Step 8: Checking tunnel status...${NC}"
sleep 3
sudo systemctl status cloudflared-fileshare --no-pager
echo ""

echo "================================================"
echo -e "${GREEN}Cloudflare Tunnel Setup Complete!${NC}"
echo "================================================"
echo ""
echo -e "${GREEN}Your application is now accessible at:${NC}"
echo "  https://$DOMAIN_NAME/fileshare"
echo ""
echo -e "${YELLOW}Useful Commands:${NC}"
echo "  Check tunnel status:  sudo systemctl status cloudflared-fileshare"
echo "  View tunnel logs:     sudo journalctl -u cloudflared-fileshare -f"
echo "  Restart tunnel:       sudo systemctl restart cloudflared-fileshare"
echo "  Stop tunnel:          sudo systemctl stop cloudflared-fileshare"
echo "  List all tunnels:     cloudflared tunnel list"
echo ""
echo -e "${YELLOW}Update .env file:${NC}"
echo "  Change APP_URL to: https://$DOMAIN_NAME/fileshare"
echo ""
echo "================================================"
